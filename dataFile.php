<?php
/**
 * 数据文件存储类
 * 
 * @author 海彬
 */
class DataFile {
	
	private $data;
	private $file;
	private $error = array();
	
	function __construct($file_name) {
		
		$this->file = 'data/'.$file_name.'.php';
		
		if (file_exists($this->file)){
			
			$this->data = include($this->file);
		}
		else{
			exit('未发现数据文件：' . $this->file);	
		}
			
		
   }
	/*
	 * $where: array( array('name','=', 'aaa'), array('cat_id', '>', 5))
	 * $sort: array('id', 'asc')
	 * $limit: array(10,5)
	 */
	public function getAll($where = array(), $sort=array(), $limit = array() ){
		
		$data = $this->data;
		
		//剔除不满足条件的
		if(!empty($where)){
			
			if(!is_array($where[0])){
				
				foreach($this->data as $key=>$row){
					
					if($this->verdict($row, $where) == false){
						unset($data[$key]);
					}
				}
			}
			else{
				
				foreach($this->data as $key=>$row){
					
					foreach($where as $w){
						
						if($this->verdict($row, $w) == false){
							unset($data[$key]);
						}
					}
				}
			}
		}
		
		//排序
		if(!empty($sort)){
			if($sort[0] == 'id'){
				strtolower($sort[1]) == 'asc' ? ksort($data) : krsort($data);
			}
			else{
				$data = array_sort($data, $sort[0], $sort[1]);
			}
		}
		
		//limit
		if(!empty($limit)){
			$data = array_slice($data, $limit[0], $limit[1], true);
		}
		
		
		return $data;
	}
	
	
	public function getOne($key = NULL){
		
		if($key !== NULL){
		
			return isset($this->data[$key]) ? $this->data[$key] : array();
		}
		else{
			return array();
		}
	}
	
	
	public function getCount($where = array()){
		
		return count($this->getAll($where));

	}
	
	
	public function insert($insert_data){
		
		if(isset($insert_data['id'])){
			
			if(isset($this->data[$insert_data['id']])){
				
				$this->error = array('msg' => 'ID已存在');
				return false;
			}
			else{
				
				$this->data[$insert_data['id']] = $insert_data;
			}
		}
		else{
			
			array_push($this->data, array());
			end($this->data);
			$key = key( $this->data );
			
			$this->data[$key] = $insert_data = array_merge(array('id'=>$key), $insert_data);
		}
		
		$this->save_file();
		return $insert_data;
	}
	
	
	public function update($key = NULL, $update_data = NULL){
		
		if($key == NULL || $this->data[$key] == NULL || $update_data == NULL){
			
			$this->error = array('msg' => '更新的数据不存在');
			return false;
		}
		
		//更新的数据中有Id
		if(isset($update_data['id']) && $key != $update_data['id']){
			
			if(isset($this->data[$update_data['id']]) ){
					
				$this->error = array('msg' => 'ID已存在');
				return false;
			}	
			else{
				//因为修改id和key，拿老数据新id新建一条，然后再删除老数据
				$old_data = $this->data[$key];
				$old_data['id'] = $update_data['id'];
				$new_data = $this->insert($old_data);
				$this->delete($key);
				$key = $new_data['id'];
			}
			
		}
		
		foreach($update_data as $k=>$v){
			
			$this->data[$key][$k] = $v;
		}
		
		$this->save_file();
	}
	
	
	public function delete($key = NULL){
		
		if($key !== NULL){
			unset($this->data[$key]);
			$this->save_file();
		}
	}
	
	
	public function save_file(){
		
		$myfile = fopen($this->file, "w") or die("Unable to open file!");
		fwrite($myfile, '<?php ');
		fwrite($myfile, 'return ' . var_export($this->data, true));
		fwrite($myfile, ' ?>');
		fclose($myfile);	
	}
	
	
	/**********************工具方法*********************************************/
	
	/*
	 * 数据对比
	 */
	public function verdict($row, $where){
		
		switch($where[1]){
			case '=':
				return $row[$where[0]] == $where[2];
				 
			case '>':
				return $row[$where[0]] > $where[2];
			
			case '<':
				return $row[$where[0]] < $where[2];
			
			case '>=':
				return $row[$where[0]] >= $where[2];
				
			case '<=':
				return $row[$where[0]] <= $where[2];
		}
	}
	
	
	/*
	 * 多维排序
	 * $array 要排序的数组
	 * $field 为要用来排序的字段
	 * $sort 默认为升序排序 
	 */
	function array_sort($array, $field, $sort='asc'){  
		
		$keysvalue = $new_array = array();  
		
		foreach ($array as $k=>$v){  
			$keysvalue[$k] = $v[$field];  
		}  
		
		if(strtolower($sort) == 'asc'){  
			asort($keysvalue);  
		}else{  
			arsort($keysvalue);  
		}  
		reset($keysvalue);  
		
		foreach ($keysvalue as $k=>$v){  
			$new_array[$k] = $array[$k];  
		}
		
		return $new_array;  
	}
	
}
?>
