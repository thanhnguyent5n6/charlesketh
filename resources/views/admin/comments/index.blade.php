@extends('admin.app')
@section('breadcrumb')
<li>
    <span> {{ $pageTitle }} </span>
</li>
@endsection
@section('content')
<div class="row">
	@include('admin.blocks.messages')
	<div class="col-md-12">
        <div class="profile-sidebar">
            <div class="portlet light profile-sidebar-portlet">
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name"> Bình luận </div>
                </div>
                <div class="profile-userbuttons">
                    <button type="button" class="btn btn-circle green btn-sm btn-comment-approved">Tất cả</button>
                    <button type="button" class="btn btn-circle default btn-sm btn-comment-unapproved">Chưa duyệt</button>
                </div>
                <div class="profile-usermenu">
                    <ul class="nav nav-list-item-comment">
                        @forelse($comments as $comment)
                        @php
                            if( $comment->category_id ){
                                $table = 'categories';
                                $recordID = $comment->category_id;
                                $name = get_table_attribute('category_languages','category_id','title',$recordID);
                            }elseif( $comment->product_id ){
                                $table = 'products';
                                $recordID = $comment->product_id;
                                $name = get_table_attribute('product_languages','product_id','title',$recordID);
                            }else{
                                $table = 'posts';
                                $recordID = $comment->post_id;
                                $name = get_table_attribute('post_languages','post_id','title',$recordID);
                            }
                        @endphp
                        <li>
                            <a href="#comment-{{ $comment->id }}" data-ajax="table={{ $table }}|id={{ $recordID }}">{{ $name }} {{--<span class="badge badge-success"></span>--}}</a>
                        </li>
                        @empty
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="profile-content">
            <div class="portlet light portlet-fit ">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bubble font-green"></i>
                        <span class="caption-subject bold font-green uppercase"> Bình luận</span>
                    </div>
                    <div class="actions">
                        <a href="#" id="btn-comment-delete-all" class="btn btn-sm btn-circle red"> <i class="icon-trash"></i> Xóa tất cả </a>
                    </div>
                </div>
                <div class="portlet-body" id="portlet-load-ajax">Không có dữ liệu trong bảng</div>
            </div>
        </div>
    </div>
</div>
@endsection