<section class="navbar-section">
    <div class="container">
        <div class="row">
            <div class="col-12 d-flex align-items-center">
                <div class="m-menu">

                    <div id="mobile-menu-1" class="text-left">
                        <div>
                            <button class="pull-left" id="click-menu-1"><i class="pe-7s-menu"></i>MENU</button>
                            <div class="clearfix"></div>
                        </div>
                        <ul class="navbar-mo-1">
                            <li>
                                <a href="/">TRANG CHỦ</a>
                            </li>
                            <li>
                                <a id="mobile-menu-toggle" data-carret="up" href="javascript:;">VANG, CHAMPAGNE <span class="carret-item"><i class="fa fa-caret-up" aria-hidden="true"></i></span></a>
                                <ul class="sub-menu nav-column nav-dropdown-default" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="sub-menu-cha active">
                                                <a href="javascript:;"><img width="33" height="33"
                                                                            src="https://shopruou.muatheme.com/wp-content/uploads/2019/03/icon2.jpg"
                                                                            class="_mi _before _image" alt="" aria-hidden="true">
                                                    <span class="sub-default-title">Dòng vang</span></a>
                                            </p>
                                            <div class="sub-menu-con">
                                                <p class="">
                                                    <a href="/vang-ngot">
                                                        <span class="sub-default-title">1. Vang hồng</span>
                                                    </a>
                                                </p>
                                                <p>
                                                    <a href="/vang-ngot">
                                                        <span class="sub-default-title">2. Vang bịch</span>
                                                    </a>
                                                </p>
                                                <p>
                                                    <a href="/vang-ngot">
                                                        <span class="sub-default-title">3. Champagne</span>
                                                    </a>
                                                </p>
                                                <p>
                                                    <a href="/vang-ngot">
                                                        <span class="sub-default-title">4. Vang trắng</span>
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="sub-menu-cha active">
                                                <a href="javascript:;"><img width="33" height="33"
                                                                            src="https://shopruou.muatheme.com/wp-content/uploads/2019/03/icon4.jpg"
                                                                            class="_mi _before _image" alt="" aria-hidden="true">
                                                    <span class="sub-default-title">Quốc Gia</span></a>
                                            </p>
                                            <div class="sub-menu-con">
                                                <p>
                                                    <a href="/vang-no">
                                                        <span class="sub-default-title">1. Vang pháp</span>
                                                    </a>
                                                </p>
                                                <p>
                                                    <a href="/vang-ngot">
                                                        <span class="sub-default-title">2. Vang Ý</span>
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="sub-menu-cha active">
                                                <a href="javascript:;"><img width="33" height="33"
                                                                            src="https://shopruou.muatheme.com/wp-content/uploads/2019/03/icon3.jpg"
                                                                            class="_mi _before _image" alt="" aria-hidden="true">
                                                    <span class="sub-default-title">Bia nhập khẩu</span></a>
                                            </p>
                                            <div class="sub-menu-con">
                                                <p>
                                                    <a href="/vang-ngot">
                                                        <span class="sub-default-title">1. Bia Bỉ</span>
                                                    </a>
                                                </p>
                                                <p>
                                                    <a href="/vang-ngot">
                                                        <span class="sub-default-title">2. Bia pháp</span>
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </ul>
                            </li>
                            <li>
                                <a href="/thong-tin-can-biet/ve-chung-toi.html">GIỚI THIỆU</a>
                            </li>
                            <li>
                                <a href="/thong-tin-can-biet/kien-thuc-ruou.html">KIẾN THỨC RƯỢU</a>
                            </li>
                            <li>
                                <a href="/thong-tin-can-biet/tuyen-dung.html">TUYỂN DỤNG</a>
                            </li>
                            <li>
                                <a href="/tin-tuc">TIN TỨC</a>
                            </li>
                            <li>
                                <a href="/thong-tin-can-biet/lien-he.html">LIÊN HỆ</a>
                            </li>
                        </ul>
                    </div>
                    <script>
                        const clickMenu1 = document.getElementById('click-menu-1');
                        clickMenu1.addEventListener('click', (e) => {
                            const navbarMo1 = document.querySelector('.navbar-mo-1');
                            navbarMo1.classList.toggle('active')
                        })
                    </script>
                </div>

                <nav class="d-none" id="navbar-mobile">
                            @php
                                Menu::resetMenu();
                                Menu::setOption([
                                    'open'=>['<ul class="navbar-nav">','<ul>'],
                                    'openitem'=>'<li>',
                                    'baseurl' => url('/')
                                ]);
                                Menu::setMenu($categories);
                                echo Menu::getMenu();
                            @endphp
                </nav>
                <div class="d-none d-xl-flex align-items-center">
                    <ul class="menu-home">
                        <li class="mr-4">
                            <a href="/">TRANG CHỦ</a>
                        </li>
                        <li class="mr-4">
                            <a href="javascript:;">VANG, CHAMPAGNE</a>
                            <ul class="sub-menu nav-column nav-dropdown-default">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="sub-menu-cha active">
                                            <a href="javascript:;"><img width="33" height="33"
                                                                        src="https://shopruou.muatheme.com/wp-content/uploads/2019/03/icon2.jpg"
                                                                        class="_mi _before _image" alt="" aria-hidden="true">
                                                <span class="sub-default-title">Dòng vang</span></a>
                                        </p>
                                        <p class="">
                                            <a href="/vang-ngot">
                                                <span class="sub-default-title">Vang hồng</span>
                                            </a>
                                        </p>
                                        <p>
                                            <a href="/vang-ngot">
                                                <span class="sub-default-title">Vang bịch</span>
                                            </a>
                                        </p>
                                        <p>
                                            <a href="/vang-ngot">
                                                <span class="sub-default-title">Champagne</span>
                                            </a>
                                        </p>
                                        <p>
                                            <a href="/vang-ngot">
                                                <span class="sub-default-title">Vang trắng</span>
                                            </a>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="sub-menu-cha active">
                                            <a href="javascript:;"><img width="33" height="33"
                                                                        src="https://shopruou.muatheme.com/wp-content/uploads/2019/03/icon4.jpg"
                                                                        class="_mi _before _image" alt="" aria-hidden="true">
                                                <span class="sub-default-title">Quốc Gia</span></a>
                                        </p>
                                        <p>
                                            <a href="/vang-no">
                                                <span class="sub-default-title">Vang pháp</span>
                                            </a>
                                        </p>
                                        <p>
                                            <a href="/vang-ngot">
                                                <span class="sub-default-title">Vang Ý</span>
                                            </a>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="sub-menu-cha active">
                                            <a href="javascript:;"><img width="33" height="33"
                                                                        src="https://shopruou.muatheme.com/wp-content/uploads/2019/03/icon3.jpg"
                                                                        class="_mi _before _image" alt="" aria-hidden="true">
                                                <span class="sub-default-title">Bia nhập khẩu</span></a>
                                        </p>
                                        <p>
                                            <a href="/vang-ngot">
                                                <span class="sub-default-title">Bia Bỉ</span>
                                            </a>
                                        </p>
                                        <p>
                                            <a href="/vang-ngot">
                                                <span class="sub-default-title">Bia pháp</span>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </ul>
                        </li>
                        <li class="mr-4">
                            <a href="/thong-tin-can-biet/ve-chung-toi.html">GIỚI THIỆU</a>
                        </li>
                        <li class="mr-4">
                            <a href="/thong-tin-can-biet/kien-thuc-ruou.html">KIẾN THỨC RƯỢU</a>
                        </li>
                        <li class="mr-4">
                            <a href="/thong-tin-can-biet/tuyen-dung.html">TUYỂN DỤNG</a>
                        </li>
                        <li class="mr-4">
                            <a href="/tin-tuc">TIN TỨC</a>
                        </li>
                        <li class="mr-4">
                            <a href="/thong-tin-can-biet/lien-he.html">LIÊN HỆ</a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>