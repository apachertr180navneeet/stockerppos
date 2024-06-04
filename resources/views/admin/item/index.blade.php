@extends('admin.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-bold">Item</span>
    </h5>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItem">Add Item</button>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="ItemTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Decirption</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCenterTitle">Add Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="Itemname" class="form-label">Name</label>
                    <input type="text" id="Itemname" name="Itemname" class="form-control" placeholder="Enter Name">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="decription" class="form-label">Description</label>
                    <textarea class="form-control" id="decription" rows="3"></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="unit" class="form-label">Unit</label>
                    <select class="form-select" id="unit" aria-label="Default select example">
                        <option selected="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->unit_name }}({{ $unit->unit_code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemsave">Save</button>
            </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCenterTitle">Edit Unit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <input type="hidden" id="editItemid" name="editItemid" class="form-control" placeholder="Enter Name">
                    <label for="editItemname" class="form-label">Name</label>
                    <input type="text" id="editItemname" name="editItemname" class="form-control" placeholder="Enter Name">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="editdecription" class="form-label">Description</label>
                    <textarea class="form-control" id="editdecription" rows="3"></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="editunit" class="form-label">Unit</label>
                    <select class="form-select" id="editunit" aria-label="Default select example">
                        <option selected="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->unit_name }}({{ $unit->unit_code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItemsave">Save</button>
            </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')
<script>
    $(document).ready(function() {
        // Cache DOM elements for better performance
        const elements = {
            itemTable: $('#ItemTable'),
            addItemSaveButton: $('#addItemsave'),
            editItemSaveButton: $('#editItemsave'),
            ItemnameField: $('#Itemname'),
            decriptionField: $('#decription'),
            unitField: $('#unit'),
            editItemNameField: $('#editItemname'),
            editdecriptionField: $('#editdecription'),
            editItemIdField: $('#editItemid'),
            editunitField: $('#editunit'),
            addItemModal: $('#addItem'),
            editItemModal: $('#editItem')
        };

        // Define URLs and CSRF token for AJAX requests
        const urls = {
            ajaxUrl: "{{route('admin.master.item.allitems')}}",
            storeUrl: '{{ route("admin.master.item.store") }}',
            statusUrl: "{{route('admin.master.item.status')}}",
            deleteUrl: "{{route('admin.master.item.delete')}}",
            editUrl: "{{route('admin.master.item.edit')}}",
            updateUrl: "{{route('admin.master.item.update')}}"
        };
        const token = "{{ csrf_token() }}";

        // Initialize DataTable
        function initializeDataTable() {
            if ($.fn.DataTable.isDataTable(elements.itemTable)) return;

            elements.itemTable.DataTable({
                processing: true,
                ajax: { url: urls.ajaxUrl },
                columns: [
                    { data: "name" },
                    { data: "description" },
                    { data: "unit_name" },
                    {
                        data: "status",
                        render: (data, type, row) => {
                            const statusClass = row.status === 'active' ? 'success' : 'danger';
                            return `<span class="badge bg-label-${statusClass} me-1">${capitalize(row.status)}</span>`;
                        }
                    },
                    {
                        data: "action",
                        render: (data, type, row) => {
                            const buttonClass = row.status === 'inactive' ? 'success' : 'danger';
                            const newStatus = row.status === 'inactive' ? 'active' : 'inactive';
                            return `
                                <button type="button" class="btn btn-sm btn-${buttonClass}" onclick="toggleStatus(${row.id}, '${newStatus}')">${capitalize(newStatus)}</button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="toggleDelete(${row.id})">Delete</button>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUnit" onclick="toggleEdit(${row.id})">Edit</button>`;
                        }
                    }
                ],
            });
        }

        // Function to toggle the status of a unit
        function toggleStatus(ItemId, newStatus) {
            const message = newStatus === 'active' ? 'Item is activated' : 'Item is deactivated';

            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Okay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: urls.statusUrl,
                        data: { ItemId, status: newStatus, _token: token },
                        success: (response) => handleResponse(response, `Item ${capitalize(newStatus)} Successfully`, 'Error changing status! Please contact the administrator'),
                        error: () => setFlash('error', 'An error occurred while changing status! Please contact the administrator.')
                    });
                }
            });
        }

        // Function to delete a unit
        function toggleDelete(itemId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Item will be deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Okay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: urls.deleteUrl,
                        data: { itemId, _token: token },
                        success: (response) => handleResponse(response, 'Item Deleted Successfully', 'Error deleting Item! Please contact the administrator'),
                        error: () => setFlash('error', 'An error occurred while deleting unit! Please contact the administrator.')
                    });
                }
            });
        }

        // After fetching data for edit, set the selected option in the edit modal
        function toggleEdit(itemId) {
            $.ajax({
                type: "POST",
                url: urls.editUrl,
                data: { itemId, _token: token },
                success: (response) => {
                    elements.editItemNameField.val(response.data.name);
                    elements.editdecriptionField.val(response.data.description);
                    elements.editItemIdField.val(response.data.id);

                    // Set the selected option for unit
                    elements.editunitField.val(response.data.unit_id);

                    elements.editItemModal.modal('show');
                },
                error: () => setFlash('error', 'An error occurred while fetching unit data! Please contact the administrator.')
            });
        }

        // Save new unit
        elements.addItemSaveButton.click(function(event) {
            event.preventDefault();
            elements.addItemSaveButton.prop('disabled', true);

            const itemData = {
                itemname: elements.ItemnameField.val(),
                description: elements.decriptionField.val(),
                unit: elements.unitField.val(),
                _token: token
            };

            $.ajax({
                type: "POST",
                url: urls.storeUrl,
                data: itemData,
                success: (response) => {
                    handleResponse(response, 'Item Added Successfully', response.errors);
                    elements.addItemModal.modal('hide');
                    clearFields(['ItemnameField', 'decriptionField', 'unitField']);
                },
                complete: () => elements.addItemSaveButton.prop('disabled', false),
                error: (xhr, status, error) => {
                    console.error('An error occurred:', error);
                    elements.addItemSaveButton.prop('disabled', false);
                }
            });
        });

        // Save edited unit
        elements.editItemSaveButton.click(function(event) {
            event.preventDefault();
            elements.editItemSaveButton.prop('disabled', true);

            const editData = {
                itemName: elements.editItemNameField.val(),
                description: elements.editdecriptionField.val(),
                unit: elements.editunitField.val(),
                ItemId: elements.editItemIdField.val(),
                _token: token
            };

            $.ajax({
                type: "POST",
                url: urls.updateUrl,
                data: editData,
                success: (response) => {
                    handleResponse(response, 'Item Edit Successfully', response.errors);
                    elements.editItemModal.modal('hide');
                    clearFields(['editUnitNameField', 'editUnitCodeField']);
                },
                complete: () => elements.editItemSaveButton.prop('disabled', false),
                error: (xhr, status, error) => {
                    console.error('An error occurred:', error);
                    elements.editItemSaveButton.prop('disabled', false);
                }
            });
        });

        // Helper functions
        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function handleResponse(response, successMessage, errorMessage) {
            const messageType = response.success ? 'success' : 'error';
            setFlash(messageType, response.success ? successMessage : errorMessage);
            elements.itemTable.DataTable().ajax.reload();
        }

        function clearFields(fields) {
            fields.forEach(field => elements[field].val(''));
        }

        // Attach toggle functions to the window object
        window.toggleStatus = toggleStatus;
        window.toggleDelete = toggleDelete;
        window.toggleEdit = toggleEdit;

        // Initialize the DataTable on page load
        initializeDataTable();
    });

    // Helper function to set flash messages
    function setFlash(type, message) {
        // Implement your flash message logic here
    }
</script>
@endsection
