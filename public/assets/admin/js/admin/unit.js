$(document).ready(function() {


    if (!$.fn.DataTable.isDataTable(unitTable)) {
        unitTable.DataTable({
            processing: true,
            ajax: {
                url: ajaxUrl,
            },
            columns: [
                {
                    data: "unit_code",
                    render: (data, type, row) => {
                        const url = '{{ route("admin.master.unit.edit", ":unitId") }}'.replace(':unitId', row.id);
                        return `<a href="${url}">${row.unit_code}</a>`;
                    }
                },
                {
                    data: "unit_name",
                },
                {
                    data: "status",
                    render: (data, type, row) => {
                        return row.status === 'active'
                            ? '<span class="badge bg-label-success me-1">Active</span>'
                            : '<span class="badge bg-label-danger me-1">Inactive</span>';
                    }
                },
                {
                    data: "action",
                    render: (data, type, row) => {
                        const statusButton = row.status === 'inactive'
                            ? `<button type="button" class="btn btn-sm btn-success" onclick="userStatus(${row.id}, 'active')">Active</button>`
                            : `<button type="button" class="btn btn-sm btn-danger" onclick="userStatus(${row.id}, 'inactive')">Inactive</button>`;
                        return statusButton;
                    }
                }
            ],
        });
    }

    addUnitsave.click(function(event) {
        event.preventDefault();
        addUnitsave.prop('disabled', true);

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
                const message = response.success ? 'Unit Add Successfully' : response.errors;
                const messageType = response.success ? 'success' : 'error';

                setFlesh(messageType, message);
                unitTable.DataTable().ajax.reload();
                addUnitModal.modal('hide');
                unitNameField.val('');
                unitCodeField.val('');
            },
            complete: function() {
                addUnitsave.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('An error occurred:', error);
                addUnitsave.prop('disabled', false);
            }
        });
    });
});
