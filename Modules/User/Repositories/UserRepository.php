<?php

namespace Modules\User\Repositories;

use App\Services\FileUploadService;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\Hash;


class UserRepository
{
    protected $model;

    public function __construct(
        User $model,
        protected FileUploadService $fileUploadService
    ) {
        $this->model = $model;
    }

    public function all(array $filters = [])
    {
        $query = $this->model->query();

        if (!empty($filters['firstname'])) {
            $query->where('firstname', 'LIKE', '%' . $filters['firstname'] . '%');
        }

        if (!empty($filters['lastname'])) {
            $query->where('lastname', 'LIKE', '%' . $filters['lastname'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'LIKE', '%' . $filters['email'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['invited_id'])) {
            $query->where('invited_id', $filters['invited_id']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function signup(array $data)
    {
        return $this->model->create($data);
    }

    public function findBy(array $conditions)
    {
        return $this->model->where($conditions)->first();
    }

    public function updatePassword(int $id, string $hashedPassword)
    {
        $user = $this->model->find($id);

        $user->password = Hash::make($hashedPassword);
        $user->verification_token = null;
        $user->active_email = 1;
        $user->status = 1;
        $user->save();

        return $user;
    }

    public function acceptInvitation(int $id, string $password)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return null;
        }

        $user->password = Hash::make($password);
        $user->verification_token = null;
        $user->active_email = 1;
        $user->status = 1;
        $user->save();
        return $user;
    }

    public function updateToken(int $id, string $verification_token)
    {
        $user = $this->model->find($id);

        $user->verification_token = $verification_token;
        return $user->save();
    }

    public function update(int $id, array $data)
    {
        $user = $this->model->find($id);

        if (!$user) {
            return false;
        }

        $user->invited_id = $data['invited_id'] ?? $user->invited_id;
        $user->firstname = $data['firstname'] ?? $user->firstname;
        $user->lastname = $data['lastname'] ?? $user->lastname;
        $user->email = $data['email'] ?? $user->email;
        $user->mobile = $data['mobile'] ?? $user->mobile;
        $user->image = $data['image'] ?? $user->image;
        $user->status = $data['status'] ?? $user->status;
        $user->active_email = $data['active_email'] ?? $user->active_email;
        $user->google_id = $data['google_id'] ?? $user->google_id;
        $user->save();

        return $user;
    }

    public function uploadProfileImage($id, $image)
    {
        $user = $this->model->find($id);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $result = $this->fileUploadService->uploadImageAsWebp(
            $image,
            'images/' . $user->id . '/profile',
            ['filename_style' => 'timestamp']
        );

        $filename = $result['filename'];

        $user->image = $filename;
        $user->save();

        return $filename;
    }

    public function paginateWithSearch(
        $perPage = 15,
        $search = null,
        $id = null,
        $invited_id = null,
        $role = null,
        $status = null,
        bool $includeBrokerLinkedUsers = false
    )
    {
        $query = $this->model->query()->with('roles');

        if ($search !== null && $search !== '') {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(firstname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(CONCAT(firstname, " ", lastname)) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(mobile) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($invited_id !== null && $invited_id !== '') {
            if ($includeBrokerLinkedUsers) {
                $query->where(function ($q) use ($invited_id) {
                    $q->where('invited_id', $invited_id)
                        ->orWhereIn('id', function ($sub) use ($invited_id) {
                            $sub->select('user_id')
                                ->from('data_room_members')
                                ->where('invited_by', $invited_id)
                                ->whereNull('deleted_at');
                        })
                        ->orWhereIn('id', function ($sub) use ($invited_id) {
                            $sub->select('user_id')
                                ->from('data_room_invitations')
                                ->where('invited_by', $invited_id)
                                ->whereNotNull('user_id')
                                ->whereNull('deleted_at');
                        })
                        ->orWhereIn('id', function ($sub) use ($invited_id) {
                            $sub->select('lists.user_id')
                                ->from('lists')
                                ->join('list_brokers', 'lists.id', '=', 'list_brokers.list_id')
                                ->where('list_brokers.broker_id', $invited_id)
                                ->whereNull('lists.deleted_at')
                                ->whereNull('list_brokers.deleted_at')
                                ->whereNotNull('lists.user_id');
                        })
                        ->orWhereIn('id', function ($sub) use ($invited_id) {
                            $sub->select('list_inquiries.user_id')
                                ->from('list_inquiries')
                                ->join('lists', 'list_inquiries.list_id', '=', 'lists.id')
                                ->join('list_brokers', 'lists.id', '=', 'list_brokers.list_id')
                                ->where('list_brokers.broker_id', $invited_id)
                                ->whereNull('lists.deleted_at')
                                ->whereNull('list_brokers.deleted_at')
                                ->whereNull('list_inquiries.deleted_at')
                                ->whereNotNull('list_inquiries.user_id');
                        });
                });
            } else {
                $query->where('invited_id', $invited_id);
            }
        }

        if (!empty($role)) {
            if ($role === 'user') {
                $query->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'broker']);
                });
            } else {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            }
        }
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($id !== null && $id !== '') {
            $query->where('id', $id);
        }

        return $query->with('inviter')->orderByDesc('id')->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model
            ->with('roles:id,name,label')
            ->select('id', 'firstname', 'lastname', 'email', 'status', 'mobile', 'image', 'active_email', 'created_at')
            ->find($id);
    }


    public function deleteImage(int $id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return false;
        }
        $user->image = null;
        return $user->save();
    }

    public function delete(int $id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    public function paginateBrokersAndAdmins($perPage = 15, $search = null)
    {
        $query = $this->model->query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'broker']);
            })
            ->with('roles:id,name,label')
            ->distinct();

        if ($search !== null && $search !== '') {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(firstname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(mobile) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->paginate($perPage);
    }


    // get all admins
    public function getAllAdmins()
    {
        return $this->model->whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->get();
    }
}
