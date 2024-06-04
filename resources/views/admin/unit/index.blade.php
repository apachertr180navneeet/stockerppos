@extends('admin.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-bold">Unit</span>
    </h5>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnit">Add Unit</button>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="unitTable">
                            <thead>
                                <tr>
                                    <th>Unit Code</th>
                                    <th>Unit Name</th>
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
<div class="modal fade" id="addUnit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCenterTitle">Add Unit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col mb-3">
                    <label for="unit_name" class="form-label">Name</label>
                    <input type="text" id="unit_name" name="unit_name" class="form-control" placeholder="Enter Name">
                </div>
            </div>
            <div class="row g-2">
                <div class="col mb-0">
                    <label for="unit_code" class="form-label">Code</label>
                    <input type="text" id="unit_code" name="unit_code" class="form-control" placeholder="Enter Code">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addUnitsave">Save</button>
            </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editUnit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCenterTitle">Edit Unit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col mb-3">
                    <input type="hidden" id="edit_unit_id" name="edit_unit_id" class="form-control" placeholder="Enter Id">
                    <label for="edit_unit_name" class="form-label">Name</label>
                    <input type="text" id="edit_unit_name" name="edit_unit_name" class="form-control" placeholder="Enter Name">
                </div>
            </div>
            <div class="row g-2">
                <div class="col mb-0">
                    <label for="edit_unit_code" class="form-label">Code</label>
                    <input type="text" id="edit_unit_code" name="edit_unit_code" class="form-control" placeholder="Enter Code">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editUnitsave">Save</button>
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
        const unitTable = $('#unitTable');
        const addUnitSaveButton = $('#addUnitsave');
        const editUnitSaveButton = $('#editUnitsave');
        const unitNameField = $('#unit_name');
        const unitCodeField = $('#unit_code');
        const editUnitNameField = $('#edit_unit_name');
        const editUnitCodeField = $('#edit_unit_code');
        const editUnitIdField = $('#edit_unit_id');
        const addUnitModal = $('#addUnit');
        const editUnitModal = $('#editUnit');

        // Define URLs and CSRF token for AJAX requests
        const ajaxUrl = "{{route('admin.master.unit.allunit')}}";
        const storeUrl = '{{ route("admin.master.unit.store") }}';
        const statusUrl = "{{route('admin.master.unit.status')}}";
        const deleteUrl = "{{route('admin.master.unit.delete')}}";
        const editUrl = "{{route('admin.master.unit.edit')}}";
        const updateUrl = "{{route('admin.master.unit.update')}}";
        const token = "{{ csrf_token() }}";

        // Initialize DataTable
        function initializeDataTable() {
            if ($.fn.DataTable.isDataTable(unitTable)) return;

            unitTable.DataTable({
                processing: true,
                ajax: { url: ajaxUrl },
                columns: [
                    {
                        data: "unit_code",
                    },
                    { data: "unit_name" },
                    {
                        data: "status",
                        render: (data, type, row) => {
                            const statusClass = row.status === 'active' ? 'success' : 'danger';
                            return `<span class="badge bg-label-${statusClass} me-1">${row.status.charAt(0).toUpperCase() + row.status.slice(1)}</span>`;
                        }
                    },
                    {
                        data: "action",
                        render: (data, type, row) => {
                            const buttonClass = row.status === 'inactive' ? 'success' : 'danger';
                            const newStatus = row.status === 'inactive' ? 'active' : 'inactive';
                            return `
                                <button type="button" class="btn btn-sm btn-${buttonClass}" onclick="toggleStatus(${row.id}, '${newStatus}')">${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}</button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="toggleDelete(${row.id})">Delete</button>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUnit" onclick="toggleEdit(${row.id})">Edit</button>`;
                        }
                    }
                ],
            });
        }

        // Function to toggle the status of a unit
        function toggleStatus(unitId, newStatus) {
            const message = newStatus === 'active' ? 'Unit is activated' : 'Unit is deactivated';

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
                        url: statusUrl,
                        data: {
                            userid: unitId,
                            status: newStatus,
                            _token: token
                        },
                        success: function(response) {
                            const messageType = response.success ? 'success' : 'error';
                            setFlash(messageType, response.success ? `Unit ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)} Successfully` : 'Error changing status! Please contact the administrator');
                            unitTable.DataTable().ajax.reload();
                        },
                        error: function() {
                            setFlash('error', 'An error occurred while changing status! Please contact the administrator.');
                        }
                    });
                }
            });
        }

        // Function to delete a unit
        function toggleDelete(unitId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Unit will be deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Okay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: deleteUrl,
                        data: {
                            unitId: unitId,
                            _token: token
                        },
                        success: function(response) {
                            const messageType = response.success ? 'success' : 'error';
                            setFlash(messageType, response.success ? 'Unit Deleted Successfully' : 'Error deleting unit! Please contact the administrator');
                            unitTable.DataTable().ajax.reload();
                        },
                        error: function() {
                            setFlash('error', 'An error occurred while deleting unit! Please contact the administrator.');
                        }
                    });
                }
            });
        }

        // Function to edit a unit
        function toggleEdit(unitId) {
            $.ajax({
                type: "POST",
                url: editUrl,
                data: {
                    unitId: unitId,
                    _token: token
                },
                success: function(response) {
                    editUnitNameField.val(response.data.unit_name);
                    editUnitCodeField.val(response.data.unit_code);
                    editUnitIdField.val(response.data.id);
                    editUnitModal.modal('show');
                },
                error: function() {
                    setFlash('error', 'An error occurred while fetching unit data! Please contact the administrator.');
                }
            });
        }

        // Save new unit
        addUnitSaveButton.click(function(event) {
            event.preventDefault();
            addUnitSaveButton.prop('disabled', true);

            const unitName = unitNameField.val();
            const unitCode = unitCodeField.val();

            $.ajax({
                type: "POST",
                url: storeUrl,
                data: {
                    _token: token,
                    unitName: unitName,
                    unitCode: unitCode
                },
                success: function(response) {
                    const messageType = response.success ? 'success' : 'error';
                    setFlash(messageType, response.success ? 'Unit Added Successfully' : response.errors);
                    unitTable.DataTable().ajax.reload();
                    addUnitModal.modal('hide');
                    unitNameField.val('');
                    unitCodeField.val('');
                },
                complete: function() {
                    addUnitSaveButton.prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('An error occurred:', error);
                    addUnitSaveButton.prop('disabled', false);
                }
            });
        });

        // Save new unit
        editUnitSaveButton.click(function(event) {
            event.preventDefault();
            editUnitSaveButton.prop('disabled', true);

            const editunitName = editUnitNameField.val();
            const editunitCode = editUnitCodeField.val();
            const editunitId = editUnitIdField.val();

            $.ajax({
                type: "POST",
                url: updateUrl,
                data: {
                    _token: token,
                    unitName: editunitName,
                    unitCode: editunitCode,
                    unitId: editunitId
                },
                success: function(response) {
                    const messageType = response.success ? 'success' : 'error';
                    setFlash(messageType, response.success ? 'Unit Edit Successfully' : response.errors);
                    unitTable.DataTable().ajax.reload();
                    editUnitModal.modal('hide');
                    unitNameField.val('');
                    unitCodeField.val('');
                },
                complete: function() {
                    editUnitSaveButton.prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('An error occurred:', error);
                    editUnitSaveButton.prop('disabled', false);
                }
            });
        });

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
