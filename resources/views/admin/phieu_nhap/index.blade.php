@extends('admin.admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading">
                Phiếu nhập
            </div>
            <?php
            $message = \Illuminate\Support\Facades\Session::get('message');
            if($message){
                echo $message;
                \Illuminate\Support\Facades\Session::put('message',null);
            }
            ?>
            <div class="row w3-res-tb">
                <div class="col-sm-5 m-b-xs">
                    <select class="input-sm form-control w-sm inline v-middle">
                        <option value="0">Bulk action</option>
                        <option value="1">Delete selected</option>
                        <option value="2">Bulk edit</option>
                        <option value="3">Export</option>
                    </select>
                    <button class="btn btn-sm btn-default">Apply</button>
                </div>
                <div class="col-sm-4">
                    <a href="{{route('add-phieu-nhap')}}"><span class="btn btn-primary fa fa-plus">Thêm phiếu nhập</span></a>
                </div>
{{--                <div class="col-sm-3">--}}
{{--                    <div class="input-group">--}}
{{--                        <input type="text" class="input-sm form-control" placeholder="Search">--}}
{{--                        <span class="input-group-btn">--}}
{{--            <button class="btn btn-sm btn-default" type="button">Go!</button>--}}
{{--          </span>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
            <div class="table-responsive">
                <table id="myTable" class="table table-striped b-t b-light">
                    <thead>
                    <tr>
                        <th>Tên phiếu nhập</th>
                        <th>Nhà cung cấp</th>
                        <th>Nội dung</th>
                        <th>Tổng tiền</th>
                        <th>Người lập</th>
                        <th>Ngày cập nhật</th>
                        <th>Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($phieu_nhaps as $key => $phieu_nhap)
                        <tr>
                            <td>{{$phieu_nhap->name}}</td>
                            <td>{{$phieu_nhap->nha_cung_cap->name}}</td>
                            <td>{{$phieu_nhap->content}}</td>
                            <td>{{number_format($phieu_nhap->tong_tien,2,",",".")}} đ</td>
                            @php
                                $nguoi_lap = \App\Models\Admin::find($phieu_nhap->nguoi_lap_id);
                            @endphp
                            <td>{{$nguoi_lap->name}}</td>
                            <td>{{$phieu_nhap->updated_at}}</td>
                            <td>
                                <a href="{{route('edit-phieu-nhap',['id'=>$phieu_nhap->id])}}" class="active" ui-toggle-class="">
                                    <i class="fa fa-pen text-success text-active"></i>
                                </a>
                                <a href="{{route('delete-phieu-nhap',['id'=>$phieu_nhap->id])}}"
                                   onclick="return confirm('Bạn có chắc muốn xóa?')"
                                   class="active" ui-toggle-class=""><i class="fa fa-trash text-danger text"></i></a>
                                <a href="{{route('view-phieu-nhap',['id'=>$phieu_nhap->id])}}" class="active" ui-toggle-class="">
                                    <i class="fa fa-eye text-success text-active"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <footer class="panel-footer">
                <div class="row">

                    {{--                    <div class="col-sm-5 text-center">--}}
                    {{--                        <small class="text-muted inline m-t-sm m-b-sm">showing 20-30 of 50 items</small>--}}
                    {{--                    </div>--}}
                    <div class="col-sm-7 text-right text-center-xs">
                        <ul class="pagination pagination-sm m-t-none m-b-none">
{{--                            {{ $phieu_nhaps->links() }}--}}
                            {{--                            <li><a href=""><i class="fa fa-chevron-left"></i></a></li>--}}
                            {{--                            <li><a href="">1</a></li>--}}
                            {{--                            <li><a href="">2</a></li>--}}
                            {{--                            <li><a href="">3</a></li>--}}
                            {{--                            <li><a href="">4</a></li>--}}
                            {{--                            <li><a href=""><i class="fa fa-chevron-right"></i></a></li>--}}
                        </ul>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection
