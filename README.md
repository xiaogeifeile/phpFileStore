# phpFileStore
php的文件存储，对一些简单的不是用数据库的网站，可以使用文件存储

# 初衷
一个朋友要做一个企业站，要求不使用数据库，这样好移植还节省空间的成本。
于是就想给他直接存文件，就做了一个简单的文件存储，可以做到简单的增删改查。  
因为是文件存储，也没有索引，所以不考虑效率问题了，而且企业站数据量很少，效率可以忽略。

# 使用方法
1.使用方法很简单，直接引入就可以  
`require_once('data.class.php');`

2.默认保存和读取data目录里的文件，需要创建一个可写的data目录，可以直接在代码里修改为其他目录。
`$this->file = 'data/'.$file_name.'.php';`

3.操作数据  
`
$goods_mod = new DataFile('goods');  
$goods_list = $goods_mod->getAll(array(array('cat_id', '=', $cat_id)), array('id','desc'), array(0,10));  
`

#  其他
以后会不定期更新，增加唯一索引、字段类型等。如果您有什么建议或问题请留言，

