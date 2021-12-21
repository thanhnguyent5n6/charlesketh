<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;

use Excel;
use Cache;
use DateTime;

class UrlParserController extends Controller
{

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $crawler;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    public function __construct(Request $request, Client $client)
    {
        $this->client = $client;
    }

    public function getBody($url)
    {
        try {
            $result = $this->client->request('GET', $url);
            $body = $result->getBody();
        } catch (RequestException $exception) {
            $body = '';
        }

        return $body;
    }

    public function getPrice(Request $request){
        $data = Excel::load($request->file('code'), function($reader) {})->get();
        
        $dataExport = [];
        foreach($data as $val){
            if( $val->ma_sp ){
                $crawler = new Crawler();
                $url = 'https://www.dienmayxanh.com/tag?key='.$val->ma_sp;
                $crawler->addHtmlContent($this->getBody($url));
                $products = $crawler->filter('ul.prods.listsearch li');
                $product = $products->count() ? $products->first() : null;
                if( $product ){
                    $dataExport[$val->ma_sp]['name'] = $product->filter('a')->count() ? $product->filter('a')->attr('title') : '';
                    $dataExport[$val->ma_sp]['price'] = $product->filter('strong')->count() ? $product->filter('strong')->text() : '';
                }else{
                    $dataExport[$val->ma_sp]['name'] = '';
                    $dataExport[$val->ma_sp]['price'] = '';
                }

                $crawler = new Crawler();
                $url = 'https://dienmaycholon.vn/tu-khoa/'.$val->ma_sp;
                $crawler->addHtmlContent($this->getBody($url));
                $products = $crawler->filter('ul.list_resultproduct li');
                $product = $products->count() ? $products->first() : null;
                if( $product ){
                    $dataExport[$val->ma_sp]['name_cholon'] = $product->filter('a')->count() ? $product->filter('a')->attr('title') : '';
                    $dataExport[$val->ma_sp]['price_cholon'] = $product->filter('strong')->count() ? $product->filter('strong.price_sale')->text() : '';
                }else{
                    $dataExport[$val->ma_sp]['name_cholon'] = '';
                    $dataExport[$val->ma_sp]['price_cholon'] = '';
                }
            }
        }
        return Excel::create('Giá-SP-'.date('dmY'), function($excel) use ($dataExport) {
            $excel->sheet('Sản phẩm', function($sheet) use ($dataExport) {
                $sheet->loadView('excel.get-products-price')->with(['data'=>$dataExport]);
            });
        })->download('xlsx');
    }

    public function search(Request $request){
        if( Cache::has('compare_'.$request->input('code')) ){
            $data = Cache::get('compare_'.$request->input('code'));
        } else {
            $url = 'https://websosanh.vn/s/'.$request->input('code').'.htm';
            $crawler = new Crawler();
            $crawler->addHtmlContent($this->getBody($url));

            $data = [];

            $url = $crawler->filter('li[data-product-id] div.img-wrap a')->link()->getUri();
            $products = $crawler->filter('li[data-product-id]');
            $product = $products->count() ? $products->first() : null;
            if( $product ){
                $url = $product->filter('a')->count() ? $product->filter('a')->link()->getUri() : null;
                if($url){
                    $crawler = new Crawler();
                    $crawler->addHtmlContent($this->getBody($url));

                    $data = $crawler->filter('tr[class="line-solid"]')->each(function (Crawler $node, $i) {
                        return [
                            'id'    =>  $i,
                            'name'  =>  $node->filter('.col-product-info h3')->text(),
                            'company'  =>  $node->filter('.col-merchant img')->attr('alt'),
                            'price'  =>  $node->filter('.col-price .price')->text(),
                            'link'  =>  $node->filter('.col-product-info span')->attr('title')
                        ];
                    });

                    Cache::forever('compare_'.$request->input('code'),$data);
                }
            }
        }
        return response()->json(['items'=>$data]);
    }
}
