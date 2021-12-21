<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<div class="modal-body">
    <div class="login-wrap">
        <div class="row">
            <div class="col-md-4 logo">
                <div>
                    <h4 class="form-title">{{ __('account.create_account') }}</h4></div>
            </div>
            <div class="col-md-8">
                <div class="content">
                    <form class="register-form" role="form" method="POST" action="{{ route('frontend.register') }}">
                        {{ csrf_field() }}
                        <div class="row">@include('admin.blocks.messages')</div>
                        <div class="form-group row">
                            <label class="control-label col-md-3">{{ __('account.name') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="name" class="form-control placeholder-no-fix" value="{{ old('name') }}" placeholder="{{ __('account.name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3">Email</label>
                            <div class="col-md-9">
                                <input type="text" name="email" class="form-control placeholder-no-fix" value="{{ old('email') }}" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3">{{ __('account.phone') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="phone" class="form-control placeholder-no-fix" value="{{ old('phone') }}" placeholder="{{ __('account.phone') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3">{{ __('account.address') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="address" class="form-control placeholder-no-fix" value="{{ old('address') }}" placeholder="{{ __('account.address') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">{{ __('account.username') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="username" class="form-control placeholder-no-fix" value="{{ old('username') }}" placeholder="{{ __('account.username') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3">{{ __('account.password') }}</label>
                            <div class="col-md-9">
                                <input type="password" name="password" class="form-control placeholder-no-fix" value="" placeholder="{{ __('account.password') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3">{{ __('account.password_confirm') }}</label>
                            <div class="col-md-9">
                                <input type="password" name="password_confirmation" class="form-control placeholder-no-fix" value="" placeholder="{{ __('account.password_confirm') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 pull-right">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="policy" {{ old('policy') ? 'checked' : '' }} /> {{ __('account.i_agree') }}
                                    <a href="javascript:;">{{ __('account.terms_of_service') }} </a> &
                                    <a href="javascript:;">{{ __('account.privacy_policy') }} </a>
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions row">
                            <div class="col-md-9 pull-right">
                                <a href="{{ route('frontend.login') }}" class="btn btn-default" data-target="#ajax-modal-login" data-toggle="modal" data-dismiss="modal">{{ __('account.back') }}</a>
                                <button type="submit" class="btn btn-site pull-right">{{ __('account.register') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>