@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg m-3">
                    <div class="card-body table-full-width table-responsive">
                        <div class="row">
                            <div class="button-list-flex">
                            <!-- <a href="{{ route('user-create') }}" class="btn btn-outline-primary my-3">Create User</a> -->
                            
                                @canany(['user_list', 'user_create', 'user_edit', 'user_delete'])

                                    <a href="{{ route('user-create') }}" class="btn btn-outline-primary my-3">Create User</a>
                                @endcanany
                                @canany(['role_list', 'role_create', 'role_edit', 'role_delete'])
                                    <a href="{{ route('roles.index') }}">
                                        <button class="btn btn-primary mb-2 float-end" href>
                                            Roles & Permission
                                        </button>
                                    </a>
                                @endcanany
                                </div>
                            </div>


                            <div class="container">
                                <table class="table table-bordered data-table  table-striped">
                                    <thead class="badge-light">
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Action</th>

                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>




                        </div>

                    </div>

                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('.data-table').DataTable({
                    processing: true,
                    serverside: true,
                    ajax: {
                        url: "{{ route('user-list') }}"
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'role',
                            name: 'role'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },


                    ]
                })


            });


            $('body').on('click', '.edit', function() {
                var userId = $(this).data("id");
                window.location.href = '/user-edit/' + userId;
            });

            $('body').on('click', '.delete', function() {
                var userId = $(this).data("id");
                window.location.href = '/userdelete/' + userId;
            });
        </script>
    @endsection
