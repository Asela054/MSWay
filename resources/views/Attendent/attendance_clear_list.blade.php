<?php $page_stitle = 'Report on Employee Attendance - Ansen Gas'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fas fa-calendar-times"></i></div>
                        <span>Attendance Device Clear Log </span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="message"></div>
                    <table class="table table-striped table-bordered table-sm small" id="attendtable">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Location</th>
                            <th>Device</th>
                            <th>Deleted Time</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendance_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
            $('#attendance_collapse').addClass('show');
            $('#attendance_clear_list_link').addClass('active');

            $('#attendtable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{!! route('attendance_clear_list_dt') !!}",
                    "data": {},
                },
                columns: [
                    { data: 'user.name', name: 'user.name' },
                    { data: 'branch.location', name: 'branch.location' },
                    { data: 'device.name', name: 'device.name' },
                    { data: 'created_at', name: 'created_at' },
                ],
                "bDestroy": true,
                "order": [
                    [3, "desc"]
                ]
            });


        });
    </script>

@endsection

