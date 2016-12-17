<?
class Map{
	//Define input array
	private $input = array();
	
	//Define output array
	private $output = array('X'=>'','Y'=>null);
	
	//Defined headers array
	private $apiKey = 'qwert123654789';
	
	//Define variables and types
	private $data = array(
		'A'=>array('type'=>'boolean', 'value'=>null),
		'B'=>array('type'=>'boolean', 'value'=>null),
		'C'=>array('type'=>'boolean', 'value'=>null),
		'D'=>array('type'=>'integer', 'value'=>null),
		'E'=>array('type'=>'integer', 'value'=>null),
		'F'=>array('type'=>'integer', 'value'=>null)
	);
	
	//Set counter for number of input errors
	private $errors = 0;
		
	function __construct($in){
	   $this->verfiyApiKey();
	   $this->verifyPostMethod();
	   $this->input = $in;
	}
	
	//Verify correct API key is used
	private function verfiyApiKey(){
		if($this->apiKey != filter_input(INPUT_SERVER, 'HTTP_API_KEY')){
			$this->output = array('error'=>'Invalid API key.');
			$this->output();
			exit();
		}
	}
	
	//Verify that only POST method is used
	private function verifyPostMethod(){
		if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') != 'POST'){
			$this->output = array('error'=>'Invalid HTTP method, POST required.');
			$this->output();
			exit();
		}			
	}
	
	//Validate that input is of proper type and set
	public function validateInput(){
		if($this->input == NULL){
			$this->output = array('error'=>'Invalid input data.');
			$this->output();
			exit();
		}
		
		foreach($this->input as $k => $v){
			if($this->data[$k]['type'] != gettype($v) || !isset($v))
				$this->errors++;				
			else
				$this->data[$k]['value'] = $v;
		} 
	}
	
	//Determine to map or not
	public function run(){
		if($this->errors == 0)
			$this->mapData();
		else
			$this->output = array('error'=>'Invalid input data type(s): '.$this->errors.' error(s) found.');			
					
		$this->output();
	}
	
	//Execute mapping on valid input
	private function mapData(){
		//Calculate X
		if($this->data['A']['value'] && $this->data['B']['value'] && !$this->data['C']['value'])
			$this->output['X']= 'S';
		elseif($this->data['A']['value'] && $this->data['B']['value'] && $this->data['C']['value'])	
			$this->output['X']= 'R';
		elseif(!$this->data['A']['value'] && $this->data['B']['value'] && $this->data['C']['value'])	
			$this->output['X']= 'T';	
		else
			$this->output = array('error'=>'Invalid map data.');			

		//Calculate Y based on X
		if($this->output['X'] == 'S')	
			$this->output['Y'] = $this->data['D']['value'] + ($this->data['D']['value'] * $this->data['E']['value'] / 100);
		elseif($this->output['X'] == 'R')	
			$this->output['Y'] = $this->data['D']['value'] + ($this->data['D']['value'] * ($this->data['E']['value'] - $this->data['F']['value']) / 100);
		elseif($this->output['X'] == 'T')	
			$this->output['Y'] = $this->data['D']['value'] - ($this->data['D']['value'] * ($this->data['F']['value']) / 100);	
			
		//Specialized 1
		if($this->output['X'] == 'R')	
			$this->output['Y'] = 2 * $this->data['D']['value'] + ($this->data['D']['value'] * $this->data['E']['value'] / 100);
		
		//Specialized 2
		if($this->data['A']['value'] && $this->data['B']['value'] && !$this->data['C']['value']){
			$this->output['X']= 'T';
		}			
		elseif($this->data['A']['value'] && !$this->data['B']['value'] && $this->data['C']['value']){
			$this->output['X']= 'S';
		}
					
		if($this->output['X'] == 'S')		
			$this->output['Y'] = $this->data['F']['value'] + $this->data['D']['value'] + ($this->data['D']['value'] * $this->data['E']['value'] / 100);
	}
	
	//Output end result as map or error 
	private function output(){
		header('Content-Type: application/json');
		echo json_encode($this->output);
	}
}		
?>