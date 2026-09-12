<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Pagination\LengthAwarePaginator;

class DataTable extends Component
{
    public $headers;
    public $rows;
    public $paginations;
    public $id;
    public $searchable;
    public $title;
    public $titleCreate;
    public $resourceName;
    public $buttonCreate;
    public $customActions;
    public $emptyMessage;
    public $editMode; // direct-modal
    public $editAttributes; //add data-attribute for edit field
    public $modalId;
    public $actions;


    public function __construct(
        $headers = [],
        $rows = [],
        $paginations = [],
        $id = null,
        $title = null,
        $titleCreate = null,
        $searchable = true,
        $resourceName = null,
        $buttonCreate = true,
        $customActions = [],
        $emptyMessage = null,
        $editMode = 'redirect',
        $editAttributes = [],
        $modalId = 'editModal',
        $actions = []
    )
    {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->paginations = $paginations ?? $rows;
        $this->id = $id ?? 'table-' . uniqid();
        $this->searchable = $searchable;
        $this->title = $title ?? __('messages.global.label.data_table');
        $this->titleCreate = $titleCreate ?? __('messages.global.label.data_table');
        $this->resourceName = $resourceName;
        $this->buttonCreate = $buttonCreate;
        $this->customActions = $customActions;
        $this->emptyMessage = $emptyMessage ?? __('messages.global.label.no_data_is_available_for_display');
        $this->editMode = $editMode;
        $this->editAttributes = $editAttributes;
        $this->modalId = $modalId;
        $this->actions = $actions;
    }

    public function getPaginator()
    {
        if ($this->rows instanceof LengthAwarePaginator) {
            return $this->rows;
        }
        return null;
    }
    
    public function render()
    {
        return view('components.data-table');
    }
    
    public function hasRows()
    {
        return count($this->rows) > 0;
    }

    public function getHeaderValue($row, $header)
    {
        $key = $header['key'];
        $value = data_get($row, $key);

        if (isset($header['value']) && is_callable($header['value'])) {
            return $header['value']($value, $row);
        }

        if (isset($header['format']) && is_callable($header['format'])) {
            return $header['format']($value, $row);
        }
        
        return $value;
    }

    public function getActionRoutes()
    {
        if (!$this->resourceName) {
            return [];
        }
        
        return [
            'show' => route("{$this->resourceName}.show", ['__ID__']),
            'edit' => route("{$this->resourceName}.edit", ['__ID__']),
            'delete' => route("{$this->resourceName}.destroy", ['__ID__']),
            'update' => route("{$this->resourceName}.update", ['__ID__']),
            'apply' => route("{$this->resourceName}.apply", ['__ID__']),
            'print' => route("{$this->resourceName}.print", ['__ID__']),
        ];
    }

    public function getModalFields()
    {
        $modalFields = [];
        foreach ($this->headers as $header) {
            if (!in_array($header['key'], ['id', 'created_at', 'updated_at'])) {
                $modalFields[] = $header['key'];
            }
        }
        return $modalFields;
    }
}