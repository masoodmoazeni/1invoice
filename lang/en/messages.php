<?php

return [
    'site.title' => 'Warehouse Management System',

    'user' => [
        'fields' => [
            'employee_name' => 'Employee Name',
            'username' => 'Username',
            'password' => 'Password',
            'wadhifty' => 'Employee Name',
            'email' => 'Email',
            'user_id' => 'User ID',
            'employee_id' => 'Employee ID',
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'branch' => 'Branch',
            'section' => 'Section',
            'department' => 'Department',
            'employee_desc' => 'Fingerprint ID',
            'employee_code' => 'Employee Number',
            'role' => 'Role',
            'new_password' => 'New Password',
            'confirm_password' => 'Confirm Password',
            'enter_your_password' => 'Enter your password',
            'enter_your_username' => 'Enter your username',
        ],

        'label' => [
            'title_index' => 'User Management',
            'create_user' => 'Add User',
            'update_user' => 'Edit User',
            'show_user' => 'View User',
            'change_password' => 'Change Password',
            'profile' => 'Profile',
            'success_change_password' => 'Password changed successfully.',
        ],

        'add_role' => 'Add Role',
        'add_permission' => 'Add Permission',
        'add_new_user' => 'Add New User',

        'not_found' => 'User not found.',
        'role_not_found' => 'Role not found.',
        'permission_not_found' => 'Permission not found.',

        'created' => 'User created successfully.',
        'updated' => 'User updated successfully.',
        'deleted' => 'User deleted successfully.',

        'profile_updated' => 'Profile updated successfully.',
        'avatar_uploaded' => 'Profile picture uploaded successfully.',
        'avatar_deleted' => 'Profile picture deleted successfully.',

        'employee_was_successfully_created' => 'Employee created successfully.',
        'user_information_was_successfully_updated' => 'User information updated successfully.',
        'user_information_was_successfully_found' => 'User information retrieved successfully.',

        'error_in_creating_employee' => 'An error occurred while creating the employee.',
        'enter_employee_personal_and_professional_information' => 'Please enter the employee\'s personal and professional information.',

        'user_information_already_saved' => 'User information has already been saved.',

        'insufficient_permissions' => 'You do not have permission to perform this action.',
        'role_changed' => 'User role changed successfully.',
        'information_entered_incorrect' => 'The information entered is incorrect.',
    ],

    'role' => [
        'fields' => [
            'name' => 'System Name',
            'display_name' => 'Display Name',
            'description' => 'Description',
            'level' => 'Level',
        ],

        'label' => [
            'title_index' => 'Role Management',
            'create_role' => 'Add New Role',
            'update_role' => 'Edit Role',
            'show_role' => 'View Role',
        ],

        'messages' => [
            'roll_and_permigen_update_was_successfully' => 'Role and permissions updated successfully.',
            'name_required' => 'The role name is required.',
            'name_unique' => 'This role name has already been taken.',
            'display_name_required' => 'The display name is required.',
            'permissions_exists' => 'One or more selected permissions are invalid.',
        ],
    ],

    'item' => [
        'fields' => [
            'name' => 'Item Name',
            'warehouse_name' => 'Warehouse',
            'section' => 'Section',
            'location' => 'Location',
            'code' => 'Item Code',
            'category' => 'Category',
            'description' => 'Description',
            'company_number' => 'Company Part Number',
            'company_name' => 'Manufacturer',
            'barcode' => 'Barcode',
            'max_limit' => 'Maximum Limit',
            'min_limit' => 'Minimum Limit',
            'freeze_limit' => 'Freeze Limit',
            'danger_limit' => 'Danger Limit',
            'stop_limit' => 'Stop Limit',
            'primary_unit' => 'Primary Unit',
            'secondary_unit' => 'Secondary Unit',
            'conversion_factor' => 'Conversion Factor',
            'stock' => 'Stock',
            'note' => 'Notes',
            'photo' => 'Item Image',
            'index_company_number' => 'Part Number',
            'index_primary_unit' => 'Primary Unit Quantity',
            'index_secondary_unit' => 'Secondary Unit Quantity',
            'creator_full_name' => 'Created By',
            'index_created_at' => 'Created At',
            'Explanations' => 'Remarks',
        ],

        'label' => [
            'title_index' => 'Items',
            'create_item' => 'Add Item',
            'update_item' => 'Edit Item',
            'show_item' => 'View Item',
        ],

        'messages' => [
            'roll_and_permigen_update_was_successfully' => 'Role and permissions updated successfully.',
            'name_required' => 'The item name is required.',
            'name_unique' => 'This item name has already been registered.',
            'display_name_required' => 'The display name is required.',
            'permissions_exists' => 'One or more selected permissions are invalid.',
        ],

        'validation' => [
            'min_limit_less_than_max' => 'The minimum limit must be less than the maximum limit.',
            'freeze_limit_less_than_max' => 'The freeze limit must be less than the maximum limit.',
            'danger_limit_less_than_max' => 'The danger limit must be less than the maximum limit.',
            'stop_limit_less_than_max' => 'The stop limit must be less than the maximum limit.',
        ],
    ],

    'warehouse' => [
        'fields' => [
            'name' => 'Warehouse Name',
            'code' => 'Warehouse Code',
            'type' => 'Warehouse Type',
            'creator_full_name' => 'Created By',
            'index_created_at' => 'Created At',
        ],

        'label' => [
            'title_index' => 'Warehouses',
            'create_warehouse' => 'Add Warehouse',
            'update_warehouse' => 'Edit Warehouse',
            'show_warehouse' => 'View Warehouse',
        ],

        'messages' => [],

        'validation' => [],
    ],

    'section' => [
        'fields' => [
            'name' => 'Section Name',
            'code' => 'Section Code',
            'description' => 'Description',
            'warehouse_name' => 'Warehouse',
            'creator_full_name' => 'Created By',
            'index_created_at' => 'Created At',
        ],

        'label' => [
            'title_index' => 'Sections',
            'create_section' => 'Define Section',
            'update_section' => 'Edit Section',
            'show_section' => 'View Section',
        ],

        'messages' => [],

        'validation' => [],
    ],

    'inbound' => [
        'fields' => [
            'name' => 'Goods Receipt',
            'warehouse' => 'Warehouse',
            'section' => 'Section',
            'count_item' => 'Number of Items',
            'status' => 'Status',
            'document_number' => 'Document Number',
            'document_date' => 'Document Date',
            'index_created_at' => 'Created At',
            'code' => 'Item Code',
            'primary_unit' => 'Primary Unit',
            'secondary_unit' => 'Secondary Unit',
            'creator_full_name' => 'Created By',
            'image' => 'Item Image',
            'item' => 'Item',
            'stock' => 'Available Stock',
            'quantity' => 'Quantity',
            'description' => 'Remarks',
        ],

        'label' => [
            'title_index' => 'Goods Receipts',
            'create_inbound' => 'New Receipt',
            'update_inbound' => 'Edit Receipt',
            'show_inbound' => 'View Receipt',
            'create_new_button' => 'Add New Item',
            'primary_unit' => 'Primary Unit',
            'primary_unit_number' => 'Primary Quantity',
            'secondary_unit' => 'Secondary Unit',
            'secondary_unit_number' => 'Secondary Quantity',
            'final_approval' => 'Final Approval',
        ],

        'messages' => [
            'please_select_date' => 'Please select the document date.',
            'please_select_number' => 'Please enter the document number.',
            'please_select_item' => 'Please select an item.',
            'please_select_unit' => 'Please select a unit.',
            'please_select_quantity' => 'Please enter the quantity.',
            'item_already_added' => 'This item has already been added to the list.',
            'the_selected_amount_greater_than_the_inventory' => 'The requested quantity exceeds the available stock.',
            'please_enter_at_least_one_of_the_unit_values' => 'Please enter at least one unit quantity.',
        ],

        'validation' => [
            'you_need_to_add_item' => 'You must add at least one item.',
        ],

        'status' => [
            'new' => 'New',
            'printed' => 'Printed',
            'applied' => 'Approved',
            'denied' => 'Denied',
            'deliverd' => 'Delivered',
        ],
    ],

    'outbound' => [
        'fields' => [
            'name' => 'Goods Issue',
            'warehouse' => 'Warehouse',
            'section' => 'Section',
            'warehouse_name' => 'Warehouse',
            'count_item' => 'Number of Items',
            'status' => 'Status',
            'document_number' => 'Document Number',
            'document_date' => 'Document Date',
            'index_created_at' => 'Created At',
            'code' => 'Item Code',
            'creator_full_name' => 'Created By',
            'image' => 'Item Image',
            'item' => 'Item',
            'stock' => 'Available Stock',
            'unit' => 'Unit',
            'quantity' => 'Quantity',
            'primary-quantity' => 'Primary Quantity',
            'secondary-quantity' => 'Secondary Quantity',
            'description' => 'Remarks',
        ],

        'label' => [
            'title_index' => 'Goods Issues',
            'create_outbound' => 'New Issue',
            'update_outbound' => 'Edit Issue',
            'show_outbound' => 'View Issue',
            'create_new_button' => 'Add New Item',
            'primary_unit' => 'Primary Unit',
            'primary_unit_number' => 'Primary Quantity',
            'secondary_unit' => 'Secondary Unit',
            'secondary_unit_number' => 'Secondary Quantity',
        ],

        'messages' => [
            'please_select_date' => 'Please select the document date.',
            'please_select_number' => 'Please enter the document number.',
            'please_select_item' => 'Please select an item.',
            'please_select_unit' => 'Please select a unit.',
            'please_select_quantity' => 'Please enter the quantity.',
            'item_already_added' => 'This item has already been added to the list.',
            'the_selected_amount_greater_than_the_inventory' => 'The requested quantity exceeds the available stock.',
            'please_enter_at_least_one_of_the_unit_values' => 'Please enter at least one unit quantity.',
        ],

        'validation' => [
            'you_need_to_add_item' => 'You must add at least one item.',
        ],

        'status' => [
            'new' => 'New',
            'printed' => 'Printed',
            'applied' => 'Approved',
            'denied' => 'Denied',
            'deliverd' => 'Delivered',
        ],
    ],

    'aminmakhzan' => [
        'fields' => [
            'warehouse_name' => 'Destination (Unit Name)',
            'count_item' => 'Number of Items',
            'status' => 'Status',
            'document_number' => 'Document Number',
            'warehouse' => 'Warehouse',
            'location' => 'Location',
            'code' => 'Item Code',
            'document_date' => 'Document Date',
            'index_created_at' => 'Created At',

            'document_date_from' => 'Document Date From',
            'document_date_until' => 'Document Date To',
            'index_created_at_from' => 'Created Date From',
            'index_created_at_until' => 'Created Date To',
            'creator_full_name' => 'Created By',

            'image' => 'Item Image',
            'item' => 'Item',
            'stock' => 'Available Stock',
            'unit' => 'Unit',
            'quantity' => 'Quantity',
            'description' => 'Remarks',
        ],

        'label' => [
            'title_index' => 'Latest Warehouse Transactions',
            'create_outbound' => 'Add Request',
            'update_outbound' => 'Edit Request',
            'show_outbound' => 'View Request',
            'create_new_button' => 'Add New Item',
            'primary_unit' => 'Primary Unit',
            'primary_unit_number' => 'Primary Quantity',
            'secondary_unit' => 'Secondary Unit',
            'secondary_unit_number' => 'Secondary Quantity',
        ],

        'messages' => [
            'have_you_delivered' => 'Has the shipment been delivered?',
        ],

        'validation' => [],

        'status' => [],

        'inbound' => [
            'title' => 'Latest Goods Receipts',
        ],

        'outbound' => [
            'title' => 'Latest Goods Issues',
        ],
    ],

    'global' => [
        'base' => [
            'error' => [
                'create' => 'An error occurred while creating the record.',
                'update' => 'An error occurred while updating the record.',
                'delete' => 'An error occurred while deleting the record.',
                'not_found' => 'The requested record was not found.',
                'try_catch' => 'An unexpected error occurred. Please try again.',
            ],

            'success' => [
                'create' => 'The record has been created successfully.',
                'update' => 'The record has been updated successfully.',
                'delete' => 'The record has been deleted successfully.',
            ],

            'adding_new' => 'Add New',
            'return' => 'Back',
            'password_repetition' => 'Confirm Password',
        ],

        'alert' => [
            'success' => 'Success!',
            'danger' => 'Error!',
            'error' => 'Error!',
            'warning' => 'Warning!',
            'info' => 'Information',
            'primary' => 'Primary',
            'secondary' => 'Notification',
            'dark' => 'Alert',
        ],

        'button' => [
            'save' => 'Save',
            'search' => 'Search',
            'cancel' => 'Cancel',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ],

        'label' => [
            'option' => 'Actions',
            'show' => 'View',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'print' => 'Print',
            'apply' => 'Approve',
            'lack' => 'Shortage',
            'back' => 'Back',
            'please_select' => 'Please Select',
            'please_field_with_input' => 'Please select :default.',
            'no_data_is_available_for_display' => 'No data available.',
            'data_table' => 'Data Table',
            'please_review_the_following_errors' => 'Please review the following errors.',
            'login_was_successful' => 'Login successful.',
            'success' => 'Success',
            'information_was_successfully_found' => 'Information retrieved successfully.',
            'advanced_search' => 'Advanced Search',
            'delete_item' => 'Are you sure you want to delete this item?',
            'record_not_found' => 'Record not found.',
            'you_do_not_have_permission_to_perform_this_operation' => 'You do not have permission to perform this action.',
            'pick_date' => 'Select Date',
            'ok' => 'OK',
            'are_you_sure_about_removing' => 'Are you sure you want to delete this record?',
            'successfully_added' => 'Item added successfully.',
            'successfully_deleted' => 'Item deleted successfully.',
            'yes_delete' => 'Yes, Delete',
            'no_return' => 'No, Cancel',
            'inbound' => 'Receipt',
            'outbound' => 'Issue',
            'end_process' => 'Printing completed',
            'document_number' => 'Document Number',
            'document_date_from' => 'Document Date From',
            'document_date_until' => 'To',
            'inbound_product' => 'Goods Receipt',
            'outbound_product' => 'Goods Issue',
            'from' => 'From',
            'warehouse_name' => 'Warehouse Name',
            'item_count' => 'Number of Items',
            'status' => 'Status',
            'document_date' => 'Document Date',
            'title_table' => 'Requests List',
            'delivery_inbound' => 'Confirm Preparation',
            'delivery_outbound' => 'Confirm Receipt',
            'list_items' => 'Items List',
            'to_name_user' => 'To (Unit Name)',
            'table_item' => 'Items Table',
            'item_name' => 'Item Name',
            'code' => 'Item Code',
            'primary_unit' => 'Primary Unit',
            'unit_original' => 'Primary Quantity',
            'secondary_unit' => 'Secondary Unit',
            'unit_secondary' => 'Secondary Quantity',
        ],

        'messages' => [
            'no_access_has_been_defined_for_this_group' => 'No permissions have been assigned to this role.',
            'record_was_not_found' => 'The requested record was not found.',
            'record_was_successfully_created' => 'The record was created successfully.',
            'record_was_successfully_updated' => 'The record was updated successfully.',
            'record_was_successfully_delete' => 'The record was deleted successfully.',
            'error_has_occurred' => 'An unexpected error has occurred.',
            'please_add_at_least_one_item' => 'Please add at least one item to the request.',
            'do_you_want_me_to_enforce' => 'Are you sure you want to perform this action?',
            'are_you_sure_received_the_material' => 'Are you sure you have received the materials?',
        ],

        'error_in_creating_or_updating_record' => 'An error occurred while creating or updating the record.',

        'table' => [
            'action' => 'Actions',
            'page' => 'Page',
            'of_the' => 'of',
            'entries' => 'entries',
        ],
    ],

    'login' => [
        'username_or_email_is_required' => 'Username or email is required.',
        'login_was_successful' => 'Login successful.',
        'password_is_required' => 'Password is required.',
        'please_enter' => 'Please enter the required information.',
        'username_or_email_is_wrong' => 'The username or email address is incorrect.',
        'warehouse_management_system' => 'Warehouse Management System',
        'save_password' => 'Save Password',
        'forgot_your_password' => 'Forgot Your Password?',
        'log_in' => 'Log In',
        'login_as_wadhifti' => 'Login as wadhifti',
        'smart_warehouse_management' => 'Smart Warehouse Management… The Backbone of Your Operations',
        'name_refinery' => 'Karbala Refinery',
        'welcome_to_the_warehouse' => 'Welcome to the Warehouse Management System Dashboard',
        'please_log_in_with_your_approved_account' => 'Please log in with your approved account',
    ],

    'menu' => [
        'main' => 'Home',
        'logout' => 'Logout',

        'setting' => [
            'title' => 'Settings',
            'warehouse' => 'Warehouse Management',
            'item' => 'Item Management',
            'section' => 'Section Management',
            'document_number' => 'Document Management',
        ],

        'board' => [
            'title' => 'Dashboard',
        ],

        'user' => [
            'title' => 'Users',
            'user' => 'User Management',
            'permission' => 'Role Management',
        ],

        'operation' => [
            'title' => 'Warehouse Operations',
        ],

        'reports' => [
            'title' => 'Reports',
        ],

        'transaction' => [
            'title' => 'Inventory Transactions',
            'inbound' => 'Goods Receipt',
            'outbound' => 'Goods Issue',
        ],

        'aminmakhzan' => [
            'title' => 'Warehouse Supervisor',
            'inbound' => 'Goods Receipt',
            'outbound' => 'Goods Issue',
        ],
    ],

    'dashboard' => [
        'aminmakhan' => [
            'title' => 'Latest Incoming and Outgoing Items',
        ],

        'inbound' => [
            'title' => 'Latest Goods Receipts',
        ],

        'outbound' => [
            'title' => 'Latest Goods Issues',
        ],

        'manager' => [
            'number_items_warehouses' => 'Total Items in Warehouses',
            'minimum_number_items' => 'Items at Minimum Stock Level',
            'number_freezing_materials' => 'Items at Freeze Level',
            'number_substances_danger_zone' => 'Items at Danger Level',
            'number_articles_issued_today' => 'Items Issued Today',
            'number_items_received_today' => 'Items Received Today',
            'number_materials_repeated_today' => 'Returned Items Today',
            'number_items_shipped_today' => 'Items Shipped Today',
        ],
    ],

];
