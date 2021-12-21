<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu page-header-fixed page-sidebar-menu-light page-sidebar-menu-closed" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler"> </div>
            </li>
            <li class="nav-item start padding-tb-20">
                <a href="{{ route('admin.dashboard.index') }}" data-route="dashboard" class="nav-link">
                    <i class="icon-home"></i>
                    <span class="title">Bảng điều khiển</span>
                    <span class="selected"></span>
                </a>
            </li>
            @php
            if( check_role('san-pham') ){
                $dataSidebar = '';
                foreach( @config('siteconfig.product') as $key => $val ){
                    if( $key == 'default' || $key == 'path') continue;
                    if( !check_role($key) ) continue;
                    if( @config('siteconfig.category.'.$key) || @config('siteconfig.attribute.'.$key) ){
                        $dataSidebar .= '
                        <li class="nav-item">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-cubes" aria-hidden="true"></i>
                            <span class="title">'.$val['page-title'].'</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">';
                            if( $val['supplier'] ){
                                $dataSidebar .= '
                                <li class="nav-item">
                                    <a href="'.route('admin.supplier.index').'" data-route="supplier" class="nav-link">
                                        <span class="title">Nhà cung cấp</span>
                                    </a>
                                </li>';
                            }

                            if( $val['filter'] ){
                                $dataSidebar .= '
                                <li class="nav-item">
                                    <a href="'.route('admin.category.index',['type'=>'filter']).'" data-route="category.filter" class="nav-link">
                                        <span class="title">Bộ lọc</span>
                                    </a>
                                </li>';
                            }
                            if( @config('siteconfig.category.'.$key) ){
                                $dataSidebar .= '
                                <li class="nav-item">
                                    <a href="'.route('admin.category.index',['type'=>$key]).'" data-route="category.'.$key.'" class="nav-link ">
                                        <span class="title">'.config('siteconfig.category.'.$key.'.page-title').'</span>
                                    </a>
                                </li>';
                            }

                            $dataSidebar .= '
                            <li class="nav-item">
                                <a href="'.route('admin.product.index',['type'=>$key]).'" data-route="product.'.$key.'" class="nav-link ">
                                    <span class="title">'.$val['page-title'].'</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="'.route('admin.product.price',['type'=>$key]).'" data-route="product.'.$key.'" class="nav-link ">
                                    <span class="title">Giá SP</span>
                                </a>
                            </li>';
                            if( @config('siteconfig.attribute.'.$key) ){
                                foreach( config('siteconfig.attribute.'.$key) as $k => $v ){
                                    if( !$v ) continue;
                                    $dataSidebar .= '
                                    <li class="nav-item">
                                        <a href="'.route('admin.attribute.index',['type'=>$k]).'" data-route="attribute.'.$k.'" class="nav-link ">
                                            <span class="title">'.config('siteconfig.attribute.'.$k.'.page-title').'</span>
                                        </a>
                                    </li>';
                                }
                            }

                        $dataSidebar .= '</ul></li>';

                    }else{
                        $dataSidebar .= '
                        <li class="nav-item">
                            <a href="'.route('admin.product.index',['type'=>$key]).'" data-route="product.'.$key.'" class="nav-link ">
                                <i class="icon-exclamation"></i>
                                <span class="title">'.$val['page-title'].'</span>
                            </a>
                        </li>';
                    }
                }
                echo $dataSidebar;
            }
            @endphp

            @php
            if( check_role('slideshow') ){
                $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-picture-o" aria-hidden="true"></i>
                        <span class="title">Hình ảnh</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                foreach( @config('siteconfig.photo') as $key => $val ){
                    if( $key == 'default' || $key == 'path') continue;
                    $dataSidebar .= '
                    <li class="nav-item">
                        <a href="'.route('admin.photo.index',['type'=>$key]).'" data-route="photo.'.$key.'" class="nav-link ">
                            <span class="title">'.$val['page-title'].'</span>
                        </a>
                    </li>';
                }
                $dataSidebar .= '</ul></li>';
                echo $dataSidebar;
            }
            @endphp

            @php
            if( check_role('online') || check_role('retail') || check_role('wholesale') ){
                $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        <span class="title">Đơn hàng</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                    foreach( @config('siteconfig.order') as $key => $val ){
                        if( $key == 'default' ) continue;
                        if( !check_role($key) ) continue;
                        $dataSidebar .= '
                        <li class="nav-item">
                            <a href="'.route('admin.order.index',['type'=>$key]).'" data-route="order.'.$key.'" class="nav-link ">
                                <span class="title">'.$val['page-title'].'</span>
                                <span class="badge badge-danger">'.count_orders($key,1).'</span>
                            </a>
                        </li>';
                    }
                $dataSidebar .= '</ul></li>';
                
                echo $dataSidebar;
            }
            @endphp

            @php
            if( check_role('admin') ){
                
                $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-address-book" aria-hidden="true"></i>
                        <span class="title">Khách hàng</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                    foreach( @config('siteconfig.customer') as $key => $val ){
                        if( $key == 'default' ) continue;
                        if( !check_role($key) ) continue;
                        $dataSidebar .= '
                        <li class="nav-item">
                            <a href="'.route('admin.customer.index',['type'=>$key]).'" data-route="customer.'.$key.'" class="nav-link ">
                                <span class="title">'.$val['page-title'].'</span>
                            </a>
                        </li>';
                    }
                $dataSidebar .= '</ul></li>';
                echo $dataSidebar;
            }
            @endphp

            @if( check_role('shop') && !check_role('admin') )
                <li class="nav-item"> <a href="{{ route('admin.pos.index',['type'=>'shop']) }}" data-route="pos.shop" class="nav-link "><i class="icon-exclamation"></i><span class="title">POS</span></a> </li>
                <?php /*
                <li class="nav-item"> <a href="{{ route('admin.customer.index',['type'=>'shop']) }}" data-route="customer.shop" class="nav-link "><i class="icon-exclamation"></i><span class="title">Khách hàng</span></a> </li> */ ?>
            @endif
            {{--
            @if( check_role('warranty') )
                <li class="nav-item"> <a href="{{ route('admin.warranty.index',['type'=>'default']) }}" data-route="warranty.default" class="nav-link "><i class="icon-exclamation"></i><span class="title">Bảo hành</span></a> </li>
            @endif

            @if( check_role('delivery') )
                <li class="nav-item"> <a href="{{ route('admin.delivery.index',['type'=>'default']) }}" data-route="delivery.default" class="nav-link "><i class="icon-exclamation"></i><span class="title">Phiếu giao hàng</span></a> </li>
            @endif

            @if( check_role('prices') )
                <li class="nav-item"> <a href="{{ route('admin.price.index') }}" data-route="price.default" class="nav-link "><i class="icon-exclamation"></i><span class="title">Phiếu báo giá</span></a> </li>
            @endif
            --}}

            @if( check_role('coupons') || check_role('promotions') )
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-gift" aria-hidden="true"></i>
                    <span class="title">Khuyến mãi</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.coupon.index') }}" data-route="coupon" class="nav-link ">
                            <span class="title">Coupon</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.promotion.index') }}" data-route="promotion" class="nav-link ">
                            <span class="title">Khuyến mãi</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if( check_role('tin-tuc') && !check_role('admin') )
            @php
            $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-exclamation"></i>
                        <span class="title">Bài viết</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                foreach( @config('siteconfig.post') as $key => $val ){
                    if( $key == 'default' || $key == 'path') continue;
                    if( @config('siteconfig.category.'.$key) ){
                        $dataSidebar .= '
                        <li class="nav-item">
                            <a href="'.route('admin.category.index',['type'=>$key]).'" data-route="category.'.$key.'" class="nav-link ">
                                <span class="title">'.config('siteconfig.category.'.$key.'.page-title').'</span>
                            </a>
                        </li>';
                    }
                    $dataSidebar .= '
                    <li class="nav-item">
                        <a href="'.route('admin.post.index',['type'=>$key]).'" data-route="post.'.$key.'" class="nav-link ">
                            <span class="title">'.$val['page-title'].'</span>
                        </a>
                    </li>';
                }
                $dataSidebar .= '</ul></li>';
                echo $dataSidebar;
            @endphp
            @endif

            @if( check_role('seos') )
            <li class="nav-item">
                <a href="{{ route('admin.seo.index') }}" data-route="seo" class="nav-link">
                    <i class="icon-exclamation"></i>
                    <span class="title">Seo page</span>
                </a>
            </li>
            @endif

            @if( check_role('admin') )
            {{--
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    <span class="title">Kho hàng</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.supplier.index',['type'=>'provider']) }}" data-route="supplier.provider" class="nav-link ">
                            <span class="title"> Nhà cung cấp </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.wms_store.index') }}" data-route="wms_store" class="nav-link ">
                            <span class="title"> Kho hàng </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.wms_import.index') }}" data-route="wms_import" class="nav-link ">
                            <span class="title"> Nhập hàng </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.wms_export.index') }}" data-route="wms_export" class="nav-link ">
                            <span class="title"> Xuất hàng </span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.wms_transfer.index') }}" data-route="wms_transfer" class="nav-link ">
                            <span class="title"> Chuyển kho </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.wms_store.inventory') }}" data-route="wms_store" class="nav-link ">
                            <span class="title"> Xem tồn </span>
                        </a>
                    </li>
                </ul>
            </li>
            --}}
            @php
                $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-exclamation"></i>
                        <span class="title">Bài viết</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                foreach( @config('siteconfig.post') as $key => $val ){
                    if( $key == 'default' || $key == 'path') continue;
                    if( @config('siteconfig.category.'.$key) ){
                        $dataSidebar .= '
                        <li class="nav-item">
                            <a href="'.route('admin.category.index',['type'=>$key]).'" data-route="category.'.$key.'" class="nav-link ">
                                <span class="title">'.config('siteconfig.category.'.$key.'.page-title').'</span>
                            </a>
                        </li>';
                    }
                    $dataSidebar .= '
                    <li class="nav-item">
                        <a href="'.route('admin.post.index',['type'=>$key]).'" data-route="post.'.$key.'" class="nav-link ">
                            <span class="title">'.$val['page-title'].'</span>
                        </a>
                    </li>';
                }
                $dataSidebar .= '</ul></li>';
                echo $dataSidebar;
            @endphp

            @php
                $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-exclamation"></i>
                        <span class="title">Trang tĩnh</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                foreach( @config('siteconfig.page') as $key => $val ){
                    if( $key == 'default' || $key == 'path') continue;
                    $dataSidebar .= '
                    <li class="nav-item">
                        <a href="'.route('admin.page.index',['type'=>$key]).'" data-route="page.'.$key.'" class="nav-link ">
                            <span class="title">'.$val['page-title'].'</span>
                        </a>
                    </li>';
                }
                $dataSidebar .= '</ul></li>';
                echo $dataSidebar;
            @endphp

            @php
                $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-exclamation"></i>
                        <span class="title">Hình ảnh</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                foreach( @config('siteconfig.photo') as $key => $val ){
                    if( $key == 'default' || $key == 'path') continue;
                    $dataSidebar .= '
                    <li class="nav-item">
                        <a href="'.route('admin.photo.index',['type'=>$key]).'" data-route="photo.'.$key.'" class="nav-link ">
                            <span class="title">'.$val['page-title'].'</span>
                        </a>
                    </li>';
                }
                $dataSidebar .= '</ul></li>';
                echo $dataSidebar;
            @endphp

            @php
                $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-exclamation"></i>
                        <span class="title">Liên kết</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                foreach( @config('siteconfig.link') as $key => $val ){
                    if( $key == 'default' || $key == 'path') continue;
                    $dataSidebar .= '
                    <li class="nav-item">
                        <a href="'.route('admin.link.index',['type'=>$key]).'" data-route="link.'.$key.'" class="nav-link ">
                            <span class="title">'.$val['page-title'].'</span>
                        </a>
                    </li>';
                }
                $dataSidebar .= '</ul></li>';
                echo $dataSidebar;
            @endphp

            @php
                $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-exclamation"></i>
                        <span class="title">Đăng ký</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                foreach( @config('siteconfig.register') as $key => $val ){
                    if( $key == 'default' || $key == 'path') continue;
                    $dataSidebar .= '
                    <li class="nav-item">
                        <a href="'.route('admin.register.index',['type'=>$key]).'" data-route="register.'.$key.'" class="nav-link ">
                            <span class="title">'.$val['page-title'].'</span>
                        </a>
                    </li>';
                }
                $dataSidebar .= '</ul></li>';
                echo $dataSidebar;
            @endphp

            {{--
            <li class="nav-item">
                <a href="{{ route('admin.search.index') }}" data-route="search" class="nav-link">
                    <i class="icon-exclamation"></i>
                    <span class="title">Search</span>
                </a>
            </li>--}}

            <li class="nav-item">
                <a href="{{ route('admin.comment.index') }}" data-route="comment" class="nav-link">
                    <i class="icon-exclamation"></i>
                    <span class="title">Bình luận</span>
                </a>
            </li>
            {{--
            @php
                $dataSidebar = '
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-people"></i>
                        <span class="title">Thành viên</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">';
                foreach( @config('siteconfig.member') as $key => $val ){
                    if( $key == 'default' || $key == 'path') continue;
                    $dataSidebar .= '
                    <li class="nav-item">
                        <a href="'.route('admin.member.index',['type'=>$key]).'" data-route="member.'.$key.'" class="nav-link ">
                            <span class="title">'.$val['page-title'].'</span>
                        </a>
                    </li>';
                }
                $dataSidebar .= '</ul></li>';
                echo $dataSidebar;
            @endphp
            
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-user"></i>
                    <span class="title"> Quản trị </span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.role.index') }}" data-route="role" class="nav-link">
                            <span class="title"> Chức năng </span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.permission.index') }}" data-route="permission" class="nav-link">
                            <span class="title"> Quyền hạn </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.group.index') }}" data-route="group" class="nav-link">
                            <span class="title"> Nhóm quản trị </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.user.index', ['type'=>'admin']) }}" data-route="user.admin" class="nav-link">
                            <span class="title"> Tài khoản </span>
                        </a>
                    </li>
                </ul>
            </li>
            --}}
            <li class="nav-item">
                <a href="{{ route('admin.setting.index') }}" data-route="setting" class="nav-link">
                    <i class="icon-settings"></i>
                    <span class="title">Cấu hình</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>