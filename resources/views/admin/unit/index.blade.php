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

@endsection
@section('script')
  <script>
        const unitTable = $('#unitTable');
        const addUnitsave = $('#addUnitsave');
        const unitNameField = $('#unit_name');
        const unitCodeField = $('#unit_code');
        const addUnitModal = $('#addUnit');
        const ajaxUrl = "{{route('admin.master.unit.allunit')}}";
        const storeUrl = '{{ route("admin.master.unit.store") }}';
        const token = "{{ csrf_token() }}";
  </script>
    <script src="{{asset('assets/admin/js/admin/unit.js')}}"></script>

    {{--  function userStatus(userid,status){
        var message = '';
        if(status == 'active'){
            message = 'User able to login after active!';
        }else{
            message = 'User cannot login after Inactive!';
        }


        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Okey'
        }).then((result) => {
            if(result.isConfirmed == true) {
                $.ajax({
                    type: "POST",
                    url: "{{route('admin.users.status')}}",
                    data: {'userid':userid,'status':status,'_token': "{{ csrf_token() }}"},
                    success: function(response) {
                        if(response.success){
                            if(status == 1){
                                setFlesh('success','User Activate Successfully');
                            }else{
                                setFlesh('success','User Inactivate Successfully');
                            }
                            $('#usersTable').DataTable().ajax.reload();
                        }else{
                            setFlesh('error','There is some problem to change status!Please contact to your server adminstrator');
                        }
                    }
                });
            }else{
                $('#usersTable').DataTable().ajax.reload();
            }
        })
    }


    function deleteUser(userid){
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this user!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if(result.isConfirmed == true) {
                var url = '{{ route("admin.users.destroy", ":userid") }}';
                url = url.replace(':userid', userid);
                $.ajax({
                    type: "DELETE",
                    url: url,
                    data: {'_token': "{{ csrf_token() }}"},
                    success: function(response) {
                        if(response.success){
                            setFlesh('success','User Deleted Successfully');
                            $('#usersTable').DataTable().ajax.reload();
                        }else{
                            setFlesh('error','There is some problem to delete user!Please contact to your server adminstrator');
                        }
                    }
                });
            }
        })
    }  --}}
@endsection
