@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.user.index', ['type'=>$type]) }}"> Tài khoản </a>
    <i class="fa fa-circle"></i>
</li>
<li>
    <span> Thêm mới </span>
</li>
@endsection
@section('content')
<div class="row">
    @include('admin.blocks.messages')
    <!-- BEGIN FORM-->
    <form role="form" method="POST" action="{{ route('admin.user.store', ['type'=>$type]) }}">
        {{ csrf_field() }}

        <div class="col-lg-9 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thêm mới </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label">Họ và tên</label>
                        <div>
                            <input type="text" name="data[name]" class="form-control" value="{{ old('data.name') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <div>
                            <input type="text" name="data[email]" class="form-control" value="{{ old('data.email') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Điện thoại</label>
                        <div>
                            <input type="text" name="data[phone]" class="form-control" value="{{ old('data.phone') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Địa chỉ</label>
                        <div>
                            <input type="text" name="data[address]" class="form-control" value="{{ old('data.address') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Tỉnh / Thành phố</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control province" name="data[province_id]" data-group="user" data-live-search="true" title="-- Chọn Tỉnh / Thành phố --">
                                {{ old('data.province_id') ? '<option value="'.old('data.province_id').'" selected ></option>' : '' }}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Quận / Huyện</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control district" name="data[district_id]" data-group="user" data-live-search="true" title="-- Chọn Quận / Huyện --">
                                {{ old('data.district_id') ? '<option value="'.old('data.district_id').'" selected ></option>' : '' }}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thông tin chung </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label">  Phân quyền </label>
                        <div>
                            <select name="groups[]" class="selectpicker show-tick show-menu-arrow form-control" multiple>
                                @forelse($groups as $group)
                                <option value="{{ $group->id }}" {{ (old('groups')) ? ((in_array($group->id,old('groups'))) ? 'selected' : '') : '' }} > {{ $group->display_name }} </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    {{--
                    <div class="form-group">
                        <label class="control-label">Tên đăng nhập</label>
                        <div>
                            <input type="text" name="data[username]" class="form-control" value="{{ old('data.username') }}">
                        </div>
                    </div>
                    --}}
                    <div class="form-group">
                        <label class="control-label">Mật khẩu</label>
                        <div>
                            <input type="password" name="password" class="form-control" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Xác nhận mật khẩu</label>
                        <div>
                            <input type="password" name="password_confirmation" class="form-control" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]" multiple>
                                <option value="publish" {{ (old('status')) ? ( (in_array('publish',old('status'))) ? 'selected' : '') : 'selected' }} > Hiển thị </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Thứ tự</label>
                        <div>
                            <input type="text" name="priority" class="form-control priceFormat" value="{{ (old('priority')) ? old('priority') : 1 }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn green"> <i class="fa fa-check"></i> Lưu</button>
                        <a href="{{ url()->previous() }}" class="btn default" > Thoát </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection