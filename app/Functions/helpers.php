<?php
use App\Functions\Facades\Tool;
use App\Functions\Facades\Template;

// Tool
if (!function_exists('set_type')) {
    function set_type($type='') {
        return Tool::setType($type);
    }
}
if (!function_exists('set_meta_tags')) {
    function set_meta_tags($seo='', $lang = 'vi') {
        return Tool::setMetaTags($seo, $lang);
    }
}

if (!function_exists('check_role')) {
    function check_role($role='') {
        return Tool::checkRole($role);
    }
}

if (!function_exists('get_thumbnail')) {
    function get_thumbnail($filename, $suffix = '_small') {
        return Tool::getThumbnail($filename, $suffix);
    }
}

if (!function_exists('get_currency_vn')) {
    function get_currency_vn($number, $symbol = ' đ', $isPrefix = false) {
        return Tool::getCurrencyVN($number, $symbol, $isPrefix);
    }
}

if (!function_exists('save_image')) {
    function save_image($path, $image, $thumbs = ['_small' => ['width' => 300, 'height' => 200 ]]) {
        return Tool::saveImage($path, $image, $thumbs);
    }
}

if (!function_exists('delete_image')) {
    function delete_image($path, $thumbs = ['_small' => ['width' => 300, 'height' => 200 ]]) {
        return Tool::deleteImage($path, $thumbs);
    }
}

if (!function_exists('get_categories')) {
    function get_categories($type,$lang='vi') {
        return Tool::getCategories($type, $lang);
    }
}

if (!function_exists('get_product_by_category')) {
    function get_product_by_category($category_id,$type,$lang='vi') {
        return Tool::getProductByCategory($category_id,$type, $lang);
    }
}

if (!function_exists('get_posts')) {
    function get_posts($type,$lang='vi') {
        return Tool::getPosts($type, $lang);
    }
}

if (!function_exists('get_photos')) {
    function get_photos($type,$lang='vi') {
        return Tool::getPhotos($type, $lang);
    }
}

if (!function_exists('get_links')) {
    function get_links($type,$lang='vi') {
        return Tool::getLinks($type, $lang);
    }
}

if (!function_exists('get_pages')) {
    function get_pages($type,$lang='vi') {
        return Tool::getPages($type, $lang);
    }
}

if (!function_exists('get_attributes')) {
    function get_attributes($type,$lang='vi',$limit=100) {
        return Tool::getAttributes($type, $lang, $limit);
    }
}

if (!function_exists('get_attributes_most_used_by_product')) {
    function get_attributes_most_used_by_product($type,$limit=5,$lang='vi') {
        return Tool::getAttributesMostUsedByProduct($type, $limit, $lang);
    }
}

if (!function_exists('get_media')) {
    function get_media($attachments) {
        return Tool::getMediaLibrary($attachments);
    }
}

if (!function_exists('get_suppliers')) {
    function get_suppliers($type='default') {
        return Tool::getSuppliers($type);
    }
}

if (!function_exists('get_user')) {
    function get_user($id,$type='default') {
        return Tool::getUser($id,$type);
    }
}

if (!function_exists('get_table_attribute')) {
    function get_table_attribute($table,$field,$name,$id) {
        return Tool::getTableAttribute($table,$field,$name,$id);
    }
}

if (!function_exists('update_code')) {
    function update_code($id,$prefix) {
        return Tool::updateCode($id,$prefix);
    }
}

if (!function_exists('build_rating')) {
    function build_rating($score=1,$class='active') {
        return Tool::buildRating($score,$class);
    }
}

if (!function_exists('nice_time')) {
    function nice_time($date) {
        return Tool::niceTime($date);
    }
}



if (!function_exists('count_orders')) {
    function count_orders($type,$status) {
        return Tool::countOrders($type,$status);
    }
}

if (!function_exists('get_comments')) {
    function get_comments($data) {
        return Tool::getComments($data);
    }
}

if (!function_exists('get_product_in_warehouses')) {
    function get_product_in_warehouses($store='',$type='default') {
        return Tool::getProductInWarehouses($store,$type);
    }
}

// Template
if (!function_exists('get_template_product')) {
    function get_template_product($item,$type='san-pham',$show=4,$moreClass='') {
        return Template::getTemplateProduct($item,$type,$show,$moreClass);
    }
}
if (!function_exists('get_template_product_sale')) {
    function get_template_product_sale($item,$type='san-pham',$show=4,$moreClass='') {
        return Template::getTemplateProductSale($item,$type,$show,$moreClass);
    }
}
if (!function_exists('get_template_product_search')) {
    function get_template_product_search($item,$type='san-pham',$show=4,$moreClass='') {
        return Template::getTemplateProductSearch($item,$type,$show,$moreClass);
    }
}

if (!function_exists('get_template_product_price')) {
    function get_template_product_price($regular_price,$sale_price) {
        return Template::getTemplateProductPrice($regular_price,$sale_price);
    }
}

if (!function_exists('get_template_post')) {
    function get_template_post($item,$type='bai-viet',$show=4,$moreClass='') {
        return Template::getTemplatePost($item,$type,$show,$moreClass);
    }
}

if (!function_exists('get_template_collection')) {
    function get_template_collection($item,$type='bai-viet',$show=4,$moreClass='') {
        return Template::getTemplateCollection($item,$type,$show,$moreClass);
    }
}

if (!function_exists('get_template_single_post')) {
    function get_template_single_post($item,$type='bai-viet',$show=4,$moreClass='') {
        return Template::getTemplateSinglePost($item,$type,$show,$moreClass);
    }
}

if (!function_exists('get_template_comment')) {
    function get_template_comment($data) {
        return Template::getTemplateComment($data);
    }
}

