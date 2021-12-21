<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<div class="modal-body">
    <div class="login-wrap">
        <div class="row">
            <div class="col-md-4 logo">
                <div>
                    <h4 class="form-title">{{ __('account.login') }}</h4></div>
            </div>
            <div class="col-md-8">
                <div class="content">
                    <form class="login-form" role="form" method="POST" action="{{ route('frontend.login') }}">
                        {{ csrf_field() }}
                        <div class="row">@include('admin.blocks.messages')</div>
                        <div class="form-group row">
                            <label for="email" class="control-label col-md-3">Email</label>
                            <div class="col-md-9"><input type="text" class="form-control placeholder-no-fix" name="email" value="{{ old('email') }}" autocomplete="off" placeholder="Email"></div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="control-label col-md-3">{{ __('account.password') }}</label>
                            <div class="col-md-9"><input type="password" class="form-control placeholder-no-fix" name="password" autocomplete="off" placeholder="Password"></div>
                        </div>

                        <div class="form-actions row">
                            <div class="col-md-9 pull-right">
                                <label class="rememberme check mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>{{ __('account.remember') }}
                                    <span></span>
                                </label>
                                <button type="submit" class="btn btn-site pull-right">{{ __('account.login') }}</button>
                            </div>
                        </div>

                        <div class="login-options row">
                            <div class="col-md-9 pull-right">
                                <h4>{{ __('account.or_login_by') }}</h4>
                                <ul class="social-icons">
                                    <li>
                                        <a class="btn btn-block btn-social btn-facebook" data-original-title="facebook" href="{{ route('login.facebook') }}">
                                            <i class="fa fa-facebook"></i>
                                            {{ __('account.sign_in_with_facebook') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="btn btn-block btn-social btn-google" data-original-title="Goole Plus" href="javascript:;">
                                            <i class="fa fa-google"></i>
                                            {{ __('account.sign_in_with_google') }}
                                        </a>
                                    </li>
                                </ul>
                                <div class="forget-password">
                                    <h4>{{ __('account.forgot_password') }}</h4>
                                    <p> {!! __('account.click_here',['attribute'=>route('frontend.password.request')]) !!} </p>
                                </div>
                                <div class="create-account">
                                    <p> {!! __('account.no_account',['attribute'=>route('frontend.register')]) !!} </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>