<div class="modal fade" id="ajax-modal-offer" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
@if ( !auth()->guard('member')->check() )
<div class="modal fade" id="ajax-modal-login" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <div class="modal-body">
                <div class="login-wrap">
                    <div class="row">
                        <div class="col-lg-4 col-12 logo d-none d-lg-block">
                            <h5>{{ __('account.login') }}</h5>
                            <p> <img src="{{ asset('images/bg-create-account.jpg') }}"> </p>
                        </div>
                        <div class="col-lg-8 col-12">
                            <div class="content">
                                <form class="login-form" role="form" method="POST" action="{{ route('frontend.login') }}">
                                    <div class="form-group row">
                                        <label for="email" class="control-label col-md-3">Email</label>
                                        <div class="col-md-9 col-sm-12 col-12"><input type="text" class="form-control" name="email" value="{{ old('email') }}" autocomplete="off" placeholder="Email"></div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="password" class="control-label col-md-3">{{ __('account.password') }}</label>
                                        <div class="col-md-9 col-sm-12 col-12"><input type="password" class="form-control" name="password" autocomplete="off" placeholder="Password"></div>
                                    </div>

                                    <div class="form-actions row mb-3">
                                        <div class="col-md-9 col-sm-12 col-12 ml-auto">
                                            <div class="custom-control custom-checkbox mr-3 mt-1 float-left">
                                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} class="custom-control-input" id="rememberme">
                                                <label class="custom-control-label" for="rememberme">{{ __('account.remember') }}</label>
                                            </div>
                                            <button type="button" class="btn btn-login btn-primary float-right">{{ __('account.login') }}</button>
                                        </div>
                                    </div>

                                    <div class="login-options row">
                                        <div class="col-md-9 col-sm-12 col-12 ml-auto">
                                            <h4>{{ __('account.or_login_by') }}</h4>
                                            <ul class="social-icons">
                                                <li>
                                                    <a class="btn btn-block btn-social btn-facebook" data-original-title="facebook" href="{{ route('login.facebook') }}">
                                                        <i class="fa fa-facebook"></i>
                                                        {{ __('account.sign_in_with_facebook') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="btn btn-block btn-social btn-google" data-original-title="Goole Plus" href="{{ route('login.google') }}">
                                                        <i class="fa fa-google"></i>
                                                        {{ __('account.sign_in_with_google') }}
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="forget-password">
                                                <h4>{{ __('account.forgot_password') }}</h4>
                                                <p> {!! __('account.click_here') !!} </p>
                                            </div>
                                            <div class="create-account">
                                                <p> {!! __('account.no_account') !!} </p>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ajax-modal-register" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <div class="modal-body">
                <div class="login-wrap">
                    <div class="row">
                        <div class="col-lg-4 col-12 logo d-none d-lg-block">
                            <h5>{{ __('account.create_account') }}</h5>
                            <p> <img src="{{ asset('images/bg-create-account.jpg') }}"> </p>
                            <ul class="social-icons">
                                <li>
                                    <a class="btn btn-block btn-social btn-facebook" data-original-title="facebook" href="{{ route('login.facebook') }}">
                                        <i class="fa fa-facebook"></i>
                                        {{ __('account.sign_in_with_facebook') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="btn btn-block btn-social btn-google" data-original-title="Goole Plus" href="{{ route('login.google') }}">
                                        <i class="fa fa-google"></i>
                                        {{ __('account.sign_in_with_google') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-8 col-12">
                            <div class="content">
                                <form class="register-form" role="form" method="POST" action="{{ route('frontend.register') }}">
                                    <input type="hidden" name="type" value="normal">
                                    {{--<div class="form-group row">
                                        <label class="control-label col-md-3">{{ __('Bạn là') }}</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" name="type" {{ old('type') && old('type') == 'supplier' ? 'checked' : '' }} class="custom-control-input" value="supplier" id="supplier">
                                                <label class="custom-control-label" for="supplier">{{ __('Nhà cung cấp') }}</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" name="type" {{ old('type') && old('type') == 'normal' ? 'checked' : '' }} class="custom-control-input" value="normal" id="normal">
                                                <label class="custom-control-label" for="normal">{{ __('Người sử dụng') }}</label>
                                            </div>
                                        </div>
                                    </div>--}}
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">{{ __('account.name') }}</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="{{ __('account.name') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">{{ __('account.phone') }}</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="{{ __('account.phone') }}">
                                        </div>
                                    </div>
                                    {{--
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">{{ __('account.address') }}</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="{{ __('account.address') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Tỉnh / Thành phố</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <select class="selectpicker show-tick show-menu-arrow form-control province" name="province_id" data-live-search="true" title="-- Chọn Tỉnh / Thành phố" data-group="register">
                                                {{ old('province_id') ? '<option value="'.old('province_id').'" selected ></option>' : '' }}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Quận / Huyện</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <select class="selectpicker show-tick show-menu-arrow form-control district" name="district_id" data-live-search="true" title="-- Chọn Quận / Huyện" data-group="register">
                                                {{ old('district_id') ? '<option value="'.old('district_id').'" selected ></option>' : '' }}
                                            </select>
                                        </div>
                                    </div>
                                    --}}
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Email</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <input type="text" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email">
                                        </div>
                                    </div>
                                    {{--
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">{{ __('account.username') }}</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="{{ __('account.username') }}">
                                        </div>
                                    </div>
                                    --}}
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">{{ __('account.password') }}</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <input type="password" name="password" class="form-control" value="" placeholder="{{ __('account.password') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">{{ __('account.password_confirm') }}</label>
                                        <div class="col-md-9 col-sm-12 col-12">
                                            <input type="password" name="password_confirmation" class="form-control" value="" placeholder="{{ __('account.password_confirm') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-9 col-sm-12 col-12 ml-auto">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="policy" {{ old('policy') ? 'checked' : '' }} class="custom-control-input" id="policy">
                                                <label class="custom-control-label" for="policy">{{ __('account.i_agree') }}
                                                <a href="javascript:;">{{ __('account.terms_of_service') }} </a> &
                                                <a href="javascript:;">{{ __('account.privacy_policy') }} </a></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions row">
                                        <div class="col-md-9 col-sm-12 col-12 ml-auto">
                                            <button type="button" data-target="#ajax-modal-login" class="btn" data-toggle="modal" data-dismiss="modal">{{ __('account.back') }}</button>
                                            <button type="button" class="btn btn-register btn-primary float-right">{{ __('account.register') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ajax-modal-forget" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <div class="modal-body">
                <div class="login-wrap">
                    <h5 class="text-center"> {{ __('account.forgot_password') }} </h5>
                    <p class="text-secondary text-center"> {{ __('account.enter_email') }} </p>
                    <form class="forget-form" role="form" method="POST" action="{{ route('frontend.password.email') }}">
                        <div class="form-group">
                            <input type="text" class="form-control" name="email" value="{{ old('email') }}" autocomplete="off" placeholder="Email">
                        </div>
                        <div class="form-actions">
                            <button type="button" data-target="#ajax-modal-login" class="btn" data-toggle="modal" data-dismiss="modal">{{ __('account.back') }}</button>
                            <button type="button" class="btn btn-forgot btn-primary float-right">{{ __('account.send_reset_password') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif