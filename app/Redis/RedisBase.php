<?php
namespace App\Redis;
use Redis;

// 在框架启动的时候读取.env文件中的KEY值，并将其赋给一个常量，然后在类中使用这个常量来初始化你的私有静态属性。

define('REDIS_HOST', env('REDIS_HOST'));
define('REDIS_PORT', env('REDIS_PORT'));
define('REDIS_PASSWORD', env('REDIS_PASSWORD'));

class RedisBase
{
    private static $redis = null;
    private static $expire = 3600; //默认存储时间（秒）
    private static $host = REDIS_HOST;
    private static $port = REDIS_PORT;
    private static $password = REDIS_PASSWORD;
    private static $db = 0;
    private static $timeout = 10;

    /**
     * 初始化Redis连接
     * 所有配置参数在实例化Redis类时加入参数即可
     */
    public function __construct($config=[])
    {
        if($config && is_array($config)){
            self::config($config);
        }
        if(self::$redis==null){
            self::$redis = new Redis();
        }
        self::$redis->connect(self::$host,self::$port,self::$timeout) or die('Redis 连接失败！');
        if(!empty(self::$password)){
            self::$redis->auth(self::$password); //如果有设置密码，则需要连接密码
        }
        if((int)self::$db){
            self::$redis->select(self::$db); //选择缓存库
        }
    }
    
    //构造函数可能不起作用，则用这个初始化类 Redis::_initialize($config=[])
    public static function _initialize($config=[])
    {
        if($config && is_array($config)){
            self::config($config);
        }
        if(self::$redis==null){
            self::$redis = new Redis();
        }
        self::$redis->connect(self::$host,self::$port,self::$timeout) or die('Redis 连接失败！');
        if(!empty(self::$password)){
            self::$redis->auth(self::$password); //如果有设置密码，则需要连接密码
        }
        if((int)self::$db){
            self::$redis->select(self::$db); //选择缓存库
        }
    }

    /**
     * 加载配置参数
     * @param  array  $config 配置数组
     */
    private static function config(array $config=[])
    {
        self::$host = isset($config['host']) ? $config['host'] : REDIS_HOST; 
        self::$port = isset($config['port']) ? $config['port'] : REDIS_PORT; 
        self::$password = isset($config['password']) ? $config['password'] : REDIS_PASSWORD; 
        self::$db = isset($config['db']) ? $config['db'] : 0; 
        self::$expire = isset($config['expire']) ? $config['expire'] : 3600; 
        self::$timeout = isset($config['timeout']) ? $config['timeout'] : 10; 
    }
 

    /**
     * 关闭Redis服务器链接
     * $redis->close() 被调用以关闭与 Redis 服务器的连接。
     * 这个方法本身不会返回任何值，它只是关闭了连接。
     * 如果尝试在调用 $redis->close() 之后执行更多操作，将会导致错误，因为连接已关闭。
     */
    public static function close()
    {
        self::$redis->close();
    }
     

    /**
     * 切换到指定的数据库, 数据库索引号用数字值指定
     * @param  int $db 存储库
     * @return 
     */
    public static function selectdb($db)
    {
        self::$redis->select((int)$db);
    }

    /**
     * 创建当前数据库的备份(该命令将在 redis 安装目录中创建dump.rdb文件)
     * @return bool 成功true否则false (如果需要恢复数据，只需将备份文件 (dump.rdb) 移动到 redis 安装目录并启动服务即可)
     */
    public static function savedb()
    {
        return self::$redis->save();
    }

       /**
     * 将 key 中储存的数字值自增
     * @param  string or int $key  键名
     * @return int  返回自增后的值，如果键不存在则新创建一个值为0，并在此基础上自增，返回自增后的数值.如果键值不是可转换的整数，则返回false
     */
    public static function incr($key)
    {
        
            return self::$redis->incr($key);
        
    }


   
     /*****************hash表操作函数*******************/
     
    /**
     * 得到hash表中一个字段的值
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return string|false
     */
    public static function hGet($key,$field)
    {
        return self::$redis->hGet($key,$field);
    }
     
    /**
     * 为hash表设定一个字段的值
     * @param string $key 缓存key
     * @param string  $field 字段
     * @param string $value 值。
     * @return bool 
     */
    public static function hSet($key,$field,$value)
    {
        return self::$redis->hSet($key,$field,$value);
    }
     
    /**
     * 判断hash表中，指定field是不是存在
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return bool
     */
    public static function hExists($key,$field)
    {
        return self::$redis->hExists($key,$field);
    }
     
    /**
     * 删除hash表中指定字段 ,支持批量删除
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return int
     */
    public static function hdel($key,$field)
    {
        $fieldArr=explode(',',$field);
        $delNum=0;
 
        foreach($fieldArr as $row)
        {
            $row=trim($row);
            $delNum+=self::$redis->hDel($key,$row);
        }
 
        return $delNum;
    }
     
    /**
     * 返回hash表元素个数
     * @param string $key 缓存key
     * @return int|bool
     */
    public static function hLen($key)
    {
        return self::$redis->hLen($key);
    }
     
    /**
     * 为hash表设定一个字段的值,如果字段存在，返回false
     * @param string $key 缓存key
     * @param string  $field 字段
     * @param string $value 值。
     * @return bool
     */
    public static function hSetNx($key,$field,$value)
    {
        return self::$redis->hSetNx($key,$field,$value);
    }
     
    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array $value
     * @return array|bool
     */
    public static function hMset($key,$value)
    {
        if(!is_array($value))
            return false;
        return self::$redis->hMset($key,$value); 
    }
     
    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array|string $value string以','号分隔字段
     * @return array|bool
     */
    public static function hMget($key,$field)
    {
        if(!is_array($field))
            $field=explode(',', $field);
        return self::$redis->hMget($key,$field);
    }
     
    /**
     * 为hash表设这累加，可以负数
     * @param string $key
     * @param int $field
     * @param string $value
     * @return bool
     */
    public static function hIncrBy($key,$field,$value)
    {
        $value=intval($value);
        return self::$redis->hIncrBy($key,$field,$value);
    }
     
    /**
     * 返回所有hash表的所有字段
     * @param string $key
     * @return array|bool
     */
    public static function hKeys($key)
    {
        return self::$redis->hKeys($key);
    }
     
    /**
     * 返回所有hash表的字段值，为一个索引数组
     * @param string $key
     * @return array|bool
     */
    public static function hVals($key)
    {
        return self::$redis->hVals($key);
    }
     
    /**
     * 返回所有hash表的字段值，为一个关联数组
     * @param string $key
     * @return array|bool
     */
    public static function hGetAll($key)
    {
        return self::$redis->hGetAll($key);
    }
     
    /*********************有序集合操作*********************/
     
    /**
     * 给当前集合添加一个元素
     * 如果value已经存在，会更新order的值。
     * @param string $key
     * @param string $order 序号
     * @param string $value 值
     * @return bool
     */
    public static function zAdd($key,$order,$value)
    {
        return self::$redis->zAdd($key,$order,$value);   
    }
     
    /**
     * 给$value成员的order值，增加$num,可以为负数
     * @param string $key
     * @param string $num 序号
     * @param string $value 值
     * @return 返回新的order
     */
    public static function zinCry($key,$num,$value)
    {
        return self::$redis->zinCry($key,$num,$value);
    }
     
    /**
     * 删除值为value的元素
     * @param string $key
     * @param stirng $value
     * @return bool
     */
    public static function zRem($key,$value)
    {
        return self::$redis->zRem($key,$value);
    }
     
    /**
     * 集合以order递增排列后，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public static function zRange($key,$start,$end)
    {
        return self::$redis->zRange($key,$start,$end);
    }
     
    /**
     * 集合以order递减排列后，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public static function zRevRange($key,$start,$end)
    {
        return self::$redis->zRevRange($key,$start,$end);
    }
     
    /**
     * 集合以order递增排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public static function zRangeByScore($key,$start='-inf',$end="+inf",$option=array())
    {
        return self::$redis->zRangeByScore($key,$start,$end,$option);
    }
     
    /**
     * 集合以order递减排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public static function zRevRangeByScore($key,$start='-inf',$end="+inf",$option=array())
    {
        return self::$redis->zRevRangeByScore($key,$start,$end,$option);
    }
     
    /**
     * 返回order值在start end之间的数量
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     */
    public static function zCount($key,$start,$end)
    {
        return self::$redis->zCount($key,$start,$end);
    }
     
    /**
     * 返回值为value的order值
     * @param unknown $key
     * @param unknown $value
     */
    public static function zScore($key,$value)
    {
        return self::$redis->zScore($key,$value);
    }
     
    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * @param unknown $key
     * @param unknown $value
     */
    public static function zRank($key,$value)
    {
        return self::$redis->zRank($key,$value);
    }
     
    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * @param unknown $key
     * @param unknown $value
     */
    public static function zRevRank($key,$value)
    {
        return self::$redis->zRevRank($key,$value);
    }
     
    /**
     * 删除集合中，score值在start end之间的元素　包括start end
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     * @return 删除成员的数量。
     */
    public static function zRemRangeByScore($key,$start,$end)
    {
        return self::$redis->zRemRangeByScore($key,$start,$end);
    }
     
    /**
     * 返回集合元素个数。
     * @param unknown $key
     */
    public static function zCard($key)
    {
        return self::$redis->zCard($key);
    }
    /*********************队列操作命令************************/
     
    /**
     * 在队列尾部插入一个元素
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public static function rPush($key,$value)
    {
        return self::$redis->rPush($key,$value); 
    }
     
    /**
     * 在队列尾部插入一个元素 如果key不存在，什么也不做
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public static function rPushx($key,$value)
    {
        return self::$redis->rPushx($key,$value);
    }
     
    /**
     * 在队列头部插入一个元素
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public static function lPush($key,$value)
    {
        return self::$redis->lPush($key,$value);
    }
     
    /**
     * 在队列头插入一个元素 如果key不存在，什么也不做
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public static function lPushx($key,$value)
    {
        return self::$redis->lPushx($key,$value);
    }
     
    /**
     * 返回队列长度
     * @param unknown $key
     */
    public static function lLen($key)
    {
        return self::$redis->lLen($key); 
    }
     
    /**
     * 返回队列指定区间的元素
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     */
    public static function lRange($key,$start,$end)
    {
        return self::$redis->lrange($key,$start,$end);
    }
     
    /**
     * 返回队列中指定索引的元素
     * @param unknown $key
     * @param unknown $index
     */
    public static function lIndex($key,$index)
    {
        return self::$redis->lIndex($key,$index);
    }
     
    /**
     * 设定队列中指定index的值。
     * @param unknown $key
     * @param unknown $index
     * @param unknown $value
     */
    public static function lSet($key,$index,$value)
    {
        return self::$redis->lSet($key,$index,$value);
    }
     
    /**
     * 删除值为vaule的count个元素
     * PHP-REDIS扩展的数据顺序与命令的顺序不太一样，不知道是不是bug
     * count>0 从尾部开始
     *  >0　从头部开始
     *  =0　删除全部
     * @param unknown $key
     * @param unknown $count
     * @param unknown $value
     */
    public static function lRem($key,$count,$value)
    {
        return self::$redis->lRem($key,$value,$count);
    }
     
    /**
     * 删除并返回队列中的头元素。
     * @param unknown $key
     */
    public static function lPop($key)
    {
        return self::$redis->lPop($key);
    }
     
    /**
     * 删除并返回队列中的尾元素
     * @param unknown $key
     */
    public static function rPop($key)
    {
        return self::$redis->rPop($key);
    }
     
    /*************redis字符串操作命令*****************/
     
    /**
     * 设置一个key
     * @param unknown $key
     * @param unknown $value
     */
    public static function set($key,$value)
    {
        return self::$redis->set($key,$value);
    }
     
    /**
     * 得到一个key
     * @param unknown $key
     */
    public static function get($key)
    {
        return self::$redis->get($key);
    }
     
    /**
     * 设置一个有过期时间的key
     * @param unknown $key
     * @param unknown $expire
     * @param unknown $value
     */
    public static function setex($key,$expire,$value)
    {
        return self::$redis->setex($key,$expire,$value);
    }
     
     
    /**
     * 设置一个key,如果key存在,不做任何操作.
     * @param unknown $key
     * @param unknown $value
     */
    public static function setnx($key,$value)
    {
        return self::$redis->setnx($key,$value);
    }
     
    /**
     * 批量设置key
     * @param unknown $arr
     */
    public static function mset($arr)
    {
        return self::$redis->mset($arr);
    }
     
    /*************redis　无序集合操作命令*****************/
     
    /**
     * 返回集合中所有元素
     * @param unknown $key
     */
    public static function sMembers($key)
    {
        return self::$redis->sMembers($key);
    }
     
    /**
     * 求2个集合的差集
     * @param unknown $key1
     * @param unknown $key2
     */
    public static function sDiff($key1,$key2)
    {
        return self::$redis->sDiff($key1,$key2);
    }
     
    /**
     * 添加集合。由于版本问题，扩展不支持批量添加。这里做了封装
     * @param unknown $key
     * @param string|array $value
     */
    public static function sAdd($key,$value)
    {
        if(!is_array($value))
            $arr=array($value);
        else
            $arr=$value;
        foreach($arr as $row)
            self::$redis->sAdd($key,$row);
    }
     
    /**
     * 返回无序集合的元素个数
     * @param unknown $key
     */
    public static function scard($key)
    {
        return self::$redis->scard($key);
    }
     
    /**
     * 从集合中删除一个元素
     * @param unknown $key
     * @param unknown $value
     */
    public static function srem($key,$value)
    {
        return self::$redis->srem($key,$value);
    }
     
    /*************redis管理操作命令*****************/
     
  
     
    /**
     * 清空当前数据库
     * @return bool
     */
    public static function flushDB()
    {
        return self::$redis->flushDB();
    }
     
    /**
     * 返回当前库状态
     * @return array
     */
    public static function info()
    {
        return self::$redis->info();
    }
     
    /**
     * 同步保存数据到磁盘
     */
    public static function save()
    {
        return self::$redis->save();
    }
     
    /**
     * 异步保存数据到磁盘
     */
    public static function bgSave()
    {
        return self::$redis->bgSave();
    }
     
    /**
     * 返回最后保存到磁盘的时间
     */
    public static function lastSave()
    {
        return self::$redis->lastSave();
    }
     
    /**
     * 返回key,支持*多个字符，?一个字符
     * 只有*　表示全部
     * @param string $key
     * @return array
     */
    public static function keys($key)
    {
        return self::$redis->keys($key);
    }
     
    /**
     * 删除指定key
     * @param unknown $key
     */
    public static function del($key)
    {
        return self::$redis->del($key);
    }
     
    /**
     * 判断一个key值是不是存在
     * @param unknown $key
     */
    public static function exists($key)
    {
        return self::$redis->exists($key);
    }
     
    /**
     * 为一个key设定过期时间 单位为秒
     * @param unknown $key
     * @param unknown $expire
     */
    public static function expire($key,$expire)
    {
        return self::$redis->expire($key,$expire);
    }
     
    /**
     * 返回一个key还有多久过期，单位秒
     * @param unknown $key
     */
    public static function ttl($key)
    {
        return self::$redis->ttl($key);
    }
     
    /**
     * 设定一个key什么时候过期，time为一个时间戳
     * @param unknown $key
     * @param unknown $time
     */
    public static function exprieAt($key,$time)
    {
        return self::$redis->expireAt($key,$time);
    }
 

    /**
     * 返回当前数据库key数量
     */
    public static function dbSize()
    {
        return self::$redis->dbSize();
    }
     
    /**
     * 返回一个随机key
     */
    public static function randomKey()
    {
        return self::$redis->randomKey();
    }


    /**
     * 开启事务
     */
    public static function transation()
    {
        self::$redis->multi();
    }

    /**
     * 提交事务
     */
    public static function commit()
    {
        self::$redis->exec();
    }

    /**
     * 取消事务
     */
    public static function discard()
    {
        self::$redis->discard();
    }




    





    public static function myself()
    {
        return self::$redis;
    }
}
