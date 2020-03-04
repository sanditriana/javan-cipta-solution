<?php
/*
* README
* ======
*
* Di bawah ini adalah contoh kode untuk aplikasi CMS khusus programmer yang ingin membuat tutorial atau screncasts.
* Di setiap kode telah dituliskan komentar untuk membantu Anda memahami kebutuhan fungsional yang harus diimplementasi.
* Misi Anda adalah melengkapi potongan kode yang disediakan sehingga aplikasi dapat berjalan sesuai kebutuhan
*
*/

$app = new TestApplication;
$app->run();

class TestApplication
{
    public function run()
    {
        $post = new Post("buka Contoh kode bisa dilihat di bawah ini: [quote] Tutorial membuat CRUD dengan Laravel [youtube id=HmV4gXIkP6k] dan gist [gist id=123]");
        echo $post->getHtmlContent();
    }
}

class Post
{
    protected $content;

    /**
     * Post constructor.
     */
    public function __construct($content)
    {
        /**
         * containing content
         * @var string
         */
        $this->content = $content;

        /**
         * The array shortcode after processingContent  
         * @var array
         */
        $this->shortCode = [];

        /**
         * The array for keep final processing compare with data
         * @var array
         */
        $this->shortCodeFinal = [];

        /**
         * First mark cheking string contect 
         * @var string
         */
        $this->firstPattern = '[';

        /**
         * Second mark cheking string contect 
         * @var string
         */
        $this->secondPattern = ']';

    }

    /**
     * Initialize all function here
     * @var void
     */
    public function getHtmlContent()
    {
        //step 1 separate between text and shortcode
        $this->processingContent($this->firstPattern, $this->secondPattern, $this->content);

        //step 2 processing shortcode to array and get data url
        $this->processingShortCode();

        
        //step 3 replace original value to tranform value from step 2
        return $this->showResult();


    }

    /**
     * ProcessingContect. separate text and shortCode
     * @var void
     */
    public function processingContent($firstPattern, $secondPattern, $content)
    {
        $start = 0;
        $i = 0;


        while(( $firstIndex = strpos($content, $firstPattern, $start)) !== false ){

            $start = $firstIndex +1;       

            $secondIndex = strpos($content, $secondPattern , $start);

            if( is_numeric( $secondIndex ) ){

                $lenCharacter = abs($firstIndex - $secondIndex); 
                
                $arr[] = substr($content,$start,$lenCharacter-1);

                $this->shortCode=$arr;
            
            }
        }
    }

    /**
     * Replace and compare shortCode and Data
     * @var void
     */
    public function processingShortCode()
    {
        // Example shortcode $this->shortCode=['youtube id=HmV4gXIkP6k']
        $key = 0;
        $arrData = $this->getData();

        foreach($this->shortCode as $value)
        {
            if(strpos($value, ' ') !== false) {
                
                $arrShortCode = explode(' ', $value);
                
                $arrShortCodeValue = explode('=', $arrShortCode[1]);
                
                $arrShortCodeFinal[] = array( "source" => $arrShortCode[0], "value" => $arrShortCodeValue[1], "original" => $value );    

            }else{

                $arrShortCodeFinal[] = array( "source" => $value, "value" => '', "original" => $value) ;
            
            }
            $key++;
        }

        foreach($arrShortCodeFinal as $key_1 => $value){

            foreach($arrData as $key_2 => $valueData){

                if($value['source'] === $valueData['source']){

                    $this->shortCodeFinal[] = [
                        'original'=>$arrShortCodeFinal[$key_1]['original'],
                        'transform'=>str_replace("replace_here", $value["value"],$valueData['url'])
                    ];                

                }
            }
        }
    }

    /**
     * Show Here
     * @var string
     */
    public function showResult()
    {
        
        foreach($this->shortCodeFinal as $key => $value){
      
            $this->content= str_replace('['.$value['original'].']', $value['transform'], $this->content);   
        }
      
        return $this->content;
        

    }

    /**
     * Add data shortCode here
     * @var void
     */
    public function getData()
    {
        // Add as neeeded
        return 
        [
            array(
                'source' => 'youtube',
                'url' => 'iframe width="560" height="315" src="https://www.youtube.com/embed/replace_here" frameborder="0" allowfullscreen',
            ),
            array(
                'source' => 'gist',
                'url' =>  'script src="https://gist.github.com/you-think-you-are-special/replace_here.js"', 
            ),
            array(
                'source' => 'quote',
                'url' => $this->getCurlQuote('https://api.chucknorris.io/jokes/random')
            )
            
        ];
    }

    /**
     * Just curl function common   
     * @var string
     */
    public function getCurlQuote($url){
       $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.chucknorris.io/jokes/random",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,  
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        $arr = json_decode($response,true);

        if(!empty($arr['value'])){
            return $arr['value'];
        }
    }
}