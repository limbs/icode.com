<?PHP
/**
*** ����������ץȡ����
*** 
*** By Laurence Chen  2013��11��28��
*/
	require_once("config.php");
	//���ƴ������ʾ����
	error_reporting(E_ALL^E_NOTICE);
	require_once("includes/iUrl.class.php");
	require_once("includes/mysql.class.php");//�������ݿ���
	require_once("includes/pinyin.php");//������ת����ƴ��

	$db	= new mysql("localhost","root","","igetdata");//��ʼ�����ݿ���
	
	/* ��������
	//http://www.i3dmo.com/   
	//www.china-designer.com/         
	//www.57zfw.com/
	*/
	//$host_str =urldecode($_GET["param"]);
	//define("HOST",$host_str);
	
	define("HOST","www.china-designer.com/");  //[ע��:]�����url��ʽ: www.china-designer.com/ ������:http://
	//ץȡģʽ:
	//1��Ϊд���棡
	//2��Ϊ�����ݿ�+д����
	//3��3д���� + �����ݿ�ץͼƬ
	define("PATTERN",2);
	//��־����Ŀ¼
	define('LOG_PATH', "error/".HOST.'/');
	//��ʱ�洢Ŀ¼
	define('TEMP','temp/');
	//��������Ŀ¼
	define('CACHE',TEMP."cache/".HOST);
	//��Աͷ�񱣴��ļ���
	define('LOGO_IMG','images/logo_img/');
	//��Աͷ�񱣴��ļ���
	define('PRO_IMG','images/pro_img/');
	//����·���ļ���
	makeDirs(LOG_PATH);//������־Ŀ¼
	makeDirs(CACHE);//��������Ŀ¼
	makeDirs(LOGO_IMG);//����ͼƬ�洢Ŀ¼
	makeDirs(PRO_IMG);//����ͼƬ�洢Ŀ¼


	main();
/**
** �ɼ�"http://www.china-designer.com"��������
**************************************************************
**1_��Ҫ������վ�������ݵĲ�����(�˽���վ�ļܹ���Ʒ�ʽ,����鿴���ε�����)
**	{keywords= 
	mainbest=0 
	designerclassid=-1
	location=4
	sel_city=0
	job=2
	charges=0
	PageSize=12
	vType=2
	birthday1=0
	birthday2=0
	AccountID=
	PageNO=$i}
**
**
**
*/
function main(){
	$min=17;//��Сҳ��
	$max=17;//���ҳ��
	$m_list_page_url = array();
	for($i=$min ; $i<=$max;$i++ ){
		$m_list_page_url[] = "http://www.china-designer.com/ezx/new_designer_s/?keywords=&mainbest=0&designerclassid=-1&
		location=4&sel_city=0&job=2&charges=0&PageSize=12&vType=2&birthday1=0&birthday2=0&AccountID=&PageNO=".$i;
	}

	//�õ������Ա�ļ�԰���
	$home_id_arr = array();
	showMsg("��ʼץȡ&nbsp;&nbsp;$min&nbsp;&nbsp;ҳ��&nbsp;&nbsp;$max&nbsp;&nbsp;ҳ�����л�Ա���",true);
	foreach($m_list_page_url as $url){
		$home_id_arr = array_merge($home_id_arr,get_home_id($url));
	}
	showMsg('��Ա���ץȡ���.............................',true);

	//�����ȡ��Ա��ŵĻ�Ա��Ϣҳ��
	$m_info_list = array();
	foreach($home_id_arr as $id){
		$url_member = "http://www.china-designer.com/home/".$id."_1_1.htm";
		$url_proarea = "http://www.china-designer.com/home/".$id."_2.htm";//���ʦ��Ʒ
		$url_news = "http://www.china-designer.com/home/".$id."_4.htm";//���ʦ����

		showMsg('<font style="font-size:20px;color:blue">��ʼץȡ��Ա���Ϊ'.$id.'�Ļ�Ա������Ϣ</font>');
		$m_info_list = get_m_info($url_member);//��ȡ��Ա��Ϣ���
		//print_r($m_info_list);exit;   //���Բ��Գ��Ƿ�ȡ����Ա����Ϣ
		$memberid=insert_member($m_info_list);//���ӵ����ݿ�ȥ�������Ѿ���������ȡ�û�ԱID��
		showMsg("<font style='font-size:20px;color:red'>�������ݿ�Ļ�ԱIDΪ��".$memberid."</font>",true);
		//die($memberid);

		showMsg('<font style="font-size:20px;color:blue">��ʼץȡ��Ա������Ϣ</font>',true);
		$m_newslist_url=get_news_list($url_news);
		foreach($m_newslist_url as $k=>$v){
			if($v!="#"){
				$news_info=get_news_info("http://www.china-designer.com/home/".$v);
				showMsg('��ʼ�������µ����ݿ�');
				insert_news($news_info,$memberid,$m_info_list['4']);
				showMsg('��ɱ������µ����ݿ�');
			}
		}

		showMsg('<font style="font-size:20px;color:blue">��ʼץȡ��Ա���Ϊ'.$id.'����Ʒ��Ϣ</font>');
		$m_proarea_list = get_m_proarea($url_proarea);//��ȡ������Ա�� ��Ʒurl�б�
		
		showMsg('<br/>�˻�Ա����'.count($m_proarea_list).'����Ʒ��Ϣ');
		//ѭ��ץȡ���ʦ��Ʒ��Ϣ
		foreach($m_proarea_list as $pro_url){
				$get_m_pro_info = get_m_pro_info('http://www.china-designer.com/home/'.$pro_url);//��ȡÿ����Ʒ��Ϣ��ҳ��
				showMsg('��ʼ������Ʒ�����ݿ�');
				insert_proarea($get_m_pro_info,$memberid);
				showMsg('��Ʒ���浽���ݿ�ɹ�');
				//break;//ֻ��ȡһ����Ա����Ϣ��������
		}
		showMsg('<font style="font-size:20px;color:blue">���ץȡ��ԱIDΪ'.$id.'����Ʒ��������Ϣ</font>');

		//break;//ֻ��ȡһ����Ա����Ϣ��������
	}

}

/*
**���ܣ���Ա�б�ҳ��� ��Ա��԰ID���
**ʱ�䣺2013��12��25��
**
**By:Laurence
**������ [$reg:��һ����ά����,[0]��ʾ��ѡ��] 
*/
function get_home_id($url){
	$clurl = get_page_url($url);
	$clurl-> noReturn();
	$content = $clurl->getContent();
	preg_match_all('/<input name="c_accountid" type="checkbox" value="(\\d+)" \\/>/sim',$content,$reg);
	
	return $reg[1];
}
/*
**���ܣ�������·�����ļ��򿪲������ڱ��ص��ļ���
**ʱ�䣺2013��12��25��
**
**By:Laurence
**������
*/
function get_page_url($url){
	//��ȡ�����ļ���������·����
	$file_name = CACHE.md5($url).".html";
	$clurl = new url();
	$clurl->setUrl($url);
	if(file_exists($file_name)){
		$clurl -> setContent(file_get_contents($file_name));
	}else{
		//ץȡָ��·��������
		var_dump($clurl);
		$clurl-> gather();
		$content = $clurl->getContent();
		file_put_contents($file_name,$content);
	}
	return $clurl;
}

/*
**����:��ȡ��Ա������Ϣ����
**����:
**$url:ҳ��ĵ�ַ--��ʾ��Ա������Ϣ��ҳ��
**
**����:�ɼ����û���ͷ����Ϣ
**������
*/
function get_m_info($url){
	$clurl = get_page_url($url);
	//ȡ�������ַ�
	$clurl-> noReturn();
	$clurl-> cut('<div class="modulecontent">','<center>');
	//��ȡ�ü��������
	$content = $clurl-> getContent();
	$content = iconv("gb2312","UTF-8",$content);
	
	$member_info = array();
	preg_match_all('/<td[^>]*>(.*)<\\/td>/U',$content,$reg);//��ȡ��Ա����
	preg_match_all('/<img src=\"(.*?)\" title=\".*?\" id=\"PreviewImagesReplace\"/is',$content,$regimg);//��ȡͼƬͷ��
	//��������ͼƬ���浽����
	get_original_image($regimg[1][0]);
	$member_info=array_merge($regimg[1],$reg[1]);
	return $member_info;
}

/**
***����:����Ա��Ϣ���뵽���ݿ���
***
***����:$m_info_list:һά����
***
***������
**/
function insert_member($m_info_list){
	global $db;
	
	//ȥ��js����Ϳո����
	$memberadss=$m_info_list[26];
	$adss = preg_replace("'<script(.*?)<\/script>'is","",$memberadss);
	$adss = str_replace("&nbsp;","",$adss);
	$adss = str_replace("  ","",$adss);
	
	$loginname=pinyin(iconv("UTF-8","gb2312",$m_info_list[4]));
	// echo $loginname; exit; ����pingyin�������Ƿ�����

	if(strlen($loginname)>15){
		$loginname=substr($loginname,0,15);
	}
	if(strlen($loginname)<6){
		$loginname=$loginname.'ers'; //ƴ�ӵ�½����
	}
	showMsg("��ԱID��$m_info_list[2]��Ա���ƣ�$loginname &nbsp;&nbsp;��Ա��Ϣ�����ݿ�",true);
	if(($db->getOne("select memberid from member where loginname='".$loginname."'"))){
		$memberquery=$db->GetRs("member","memberid","where loginname='$loginname'");
		$memberid=$memberquery['memberid'];
		showErrMsg("��ԱID��$m_info_list[2]��Ա���ƣ�".$member_info_m['loginname']."�Ѵ������ݿ�>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>",true);
		return $memberid;
	}
	else{
		$ext=strrchr($m_info_list[0],".");
		$filename=get_original_image($m_info_list[0],'images/logo_img/'.$loginname.$ext);//��������ͼƬ���浽����
		$DateBirth=$m_info_list[10];//��������
		if($DateBirth!=''){
			$DateBirth=str_replace("��","-",$DateBirth);
			$DateBirth=str_replace("��","-",$DateBirth);
			$DateBirth=str_replace("��","-",$DateBirth);
		}
		$member_info_m=array(
			'loginname'=>$loginname,
			'logopwd'=>'adminadmin',  //Ĭ������
			'membertypeid'=>'1',
			'memberlogo'=>'http://localhost:8888/idata/'.$filename,//��Աͷ��      // �����޸�:URL
			'membername'=>trim($m_info_list[4]),//��Ա����
			'membersex'=>trim($m_info_list[8]),//��Ա�Ա�
			'memberadss'=>$adss,//��������
			'membertell'=>'11111111111',//��Ա�绰
			'memberqq'=>'999999999',//��ԱQQ
			'memberemail'=>'999999999@qq.com',//��Ա����
			'memberwork'=>trim($m_info_list[12]),//������λ
			'memberpost'=>$m_info_list[30],//��Աְλ
			'membercount'=>delhtml(trim($m_info_list[54])),//��Ա����
			'DateBirth'=>$DateBirth,//��������
			'College'=>trim($m_info_list[20]),//��ҵѧԺ
			'Professional'=>trim($m_info_list[22]),//רҵ
			'Workdate'=>trim($m_info_list[18]),//��������
			'Education'=>trim($m_info_list[20]),//ѧ��
			'sjstype'=>trim(delhtml($m_info_list[24])),//���ʦ����
			'sjszc'=>trim(delhtml($m_info_list[46])),//���ʦר��
			'workname'=>trim(delhtml($m_info_list[42])),//����շ�
			'ctime'=>date('Y-m-d')//ע��ʱ��
		);
		$db->Add('member',$member_info_m);
		showMsg("-----------��ԱID��$m_info_list[2]��Ա���ƣ�$m_info_list[4]&nbsp;&nbsp;��Ա��Ϣ���ɹ�----------------------",true);
		return $db->insert_id();
	}
}
/*
**����:�����Ա��Ʒ��Ϣ�����ݿ�
**����:$pro_info��Ʒ��Ϣ��$memberid:��Ա���
**
**������
**By Laurence.Chen 2014��1��27��
**
*/
function insert_proarea($pro_info,$memberid){
	global $db;
	$title=trim($pro_info[0][0]);//��Ʒ����  ������  Ĭ���ļ���
	$count=trim($pro_info[1][0]);//��Ʒ˵��  ����
	if($title=="Ĭ���ļ���"){   //???? --> $title,"Ĭ���ļ���" ���ַ������Ȳ�һ��
		$title="�ҵ���Ʒ";
		$count="";
	}
	if(false){//($db->getOne("select proareaid from proarea where proareaname='".$title."' and memberid=".$memberid))
		showErrMsg("����Ʒ---".$title."---�Ѿ����>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>");
		return;
	}
	else{
		$minaji=mt_rand(45, 150);//����������
		$money=$minaji*1000;
		$db->Add('proarea',array(
			'memberid'=>$memberid,
			'proareaname'=>$title,//����
			'proareaarea'=>$minaji,//��Ʒ���
			'proareacount'=>$count,//��Ʒ˵��
			'proareamoney'=>$money,//��Ʒ���
		));
		$pro_id=$db->insert_id();
		
		foreach($pro_info[2] as $info_url){
			$pro_info_count=get_pro_info_count("http://www.china-designer.com/home/".$info_url);
			$filename=get_original_image($pro_info_count[1]);//��������ͼƬ���浽����
			if($filename==''){
				echo '��ƷͼƬ����ʧ��';
				continue;
			}
			else{
				echo '��ƷͼƬ���سɹ�';

				$db->Add('proareapic',array(
					'proareaid'=>$pro_id,
					'proareapicname'=>$pro_info_count[0],//����
					'proareapiccount'=>'',//˵��
					'proareapicadss'=>'http://localhost:8888/idata/'.$filename,//ͼƬ��ַ   //�����޸�: URL 
					'proart'=>$pro_info_count[2],//��ǩ����
					'protype'=>$pro_info_count[3],//ͼƬ�������
					'ctime'=>date('Y-m-d'),//�ϴ�ʱ��
				));
				echo "��ƷͼƬ�������ݿ�ɹ�";
			}
			//break;
		}
	}
}

/*
***����:ץȡÿ����Ʒ�µ�ÿ������ƷͼƬ����Ϣ
***����:ҳ���url 
***
***By laurence.Chen 2014��2��8��
***������
*/
function get_pro_info_count($url){
	$clurl = get_page_url($url);
	$clurl->noReturn();
	$clurl->cut('<div class="works-catalogInfo-space">','</div><div class="clear"></div>');
	$content = $clurl->getContent();
	$content = iconv("gb2312","UTF-8",$content);
	//echo $content;exit;
	//<img src="//"   border="0" alt="//" />
	preg_match_all("/<span  class=\"title\">(.*?)<\/span>/i",$content,$title);///��ƷͼƬ����
	//print_r($title);exit;
	preg_match_all("/<span class=\"ftColor-hui\"><a\s.*?<img src=\"(.*?)\"   border=\"0\" alt=\"(.*?)\" \/>/i",$content,$val);//��Ʒ��ǩ����
	//print_r($val);exit;
	//������ǩ���Կ�ʼ
	$t=strrchr($val[2][0],'-');
	if(stripos($t,'��')>0){
		$t=substr($t,stripos($t,'��'),strlen($t));
		if(stripos($t,',')>0){
			$t=substr($t,stripos($t,',')+1,strlen($t));
		}
		if(trim($t)=='��' || trim($t)=='������'){
			$t='';
		}
	}
	//������ǩ���Խ���
	//echo $t;
	preg_match_all("/<strong>��Ŀ����<\/strong>��(.*?)<div/",$content,$key);
	//print_r($key);
	$pro_info_row[0]=delhtml($title[1][0]);
	$pro_info_row[1]=delhtml($val[1][0]);
	$pro_info_row[2]=$t;
	$pro_info_row[3]=delhtml($key[1][0]);
	//print_r($pro_info_row);exit;
	return $pro_info_row;
}


/*
**����:��ȡͼƬ��Ϣ�ķ���
**����:
**$url:��վ·��
**$filename:�ļ���
**2013��12��25��
**������
*/
function GrabImage($url,$filename="") {
	//showMsg("����ͼƬ��ַ:$url  ���ƣ�$filename");
	set_time_limit(24 * 60 * 60 * 60);//php set_time_limit�����Ĺ��������õ�ǰҳ��ִ�ж೤ʱ�䲻����Ŷ��
	if($url==""):return false;endif;
	if($filename=="") {
		$ext=strrchr($url,".");
		if($ext!=".gif" && $ext!=".jpg"):return false;endif;
		$filename=date("dMYHis").$ext;
	}
	ob_start();
	readfile($url);
	$img = ob_get_contents();
	ob_end_clean();
	$size = strlen($img);
	$fp2=@fopen($filename, "a");
	fwrite($fp2,$img);
	fclose($fp2);
	return $filename;
}

/*
**����:��ȡͼƬ��Ϣ
**
**����:�ļ�����"images/pro_img/"��ͼƬ��Ϣ
**��ʽ����:.gif/.jpg/.png/.bmp
**
** By Laurence.Chen 2014��2��8��
**������
*/
function get_original_image($url,$filename="") {
//	set_time_limit(24 * 60 * 60 * 60);//php set_time_limit�����Ĺ��������õ�ǰҳ��ִ�ж೤ʱ�䲻����Ŷ��
	if($url==""):return false;endif;
	$url = preg_replace('/ /','%20',$url);
	//���δָ��ͼƬ���֣�����ͼƬ�洢·����
	if($filename == "" ){
		$ext=strrchr($url,".");
		if($ext!=".gif" && $ext!=".jpg" && $ext!=".png" && $ext!=".bmp"):return false;endif;
		$filename='images/pro_img/'.date("dMYHis").$ext;
	}
	if (file_exists(dirname($filename)) && is_readable(dirname($filename)) && is_writable(dirname($filename))) {
		try {
			$ch = curl_init($url);
			$fp = @fopen($filename, 'w');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			fclose($fp);
			if ($code != 200) {
				@unlink($filename);
				GrabImage($url,$filename);
				throw new Exception('�޷����Զ���ļ�:'.$url." ��:".$filename);
			}
		} catch(Exception $e) {
			$filename = GrabImage($url,$filename);
			showErrMsg($e->getMessage());
		}
		showMsg("�����ļ�����ͼƬ�ɹ�");
		return $filename;
	}
	return false;
}

/*
**����: �����Ա���µ����ݿ�
**
** By Laurence.Chen  2014��2��8��
** ������
*/
function insert_news($news_info,$memberid,$memberbname){
	global $db;
	if(($db->getOne("select newsid from news where newstitle='".trim($news_info[0])."'"))){
		showErrMsg("�������Ѿ����>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>");
		return;
	}
	else{
		$db->Add('news',array(
			'memberid'=>$memberid,
			'newstitle'=>delhtml(trim($news_info[0])),
			'newsAuthor'=>$memberbname,
			'newsSummary'=>'http://www.52zsj.com',
			'newstype'=>delhtml(trim($news_info[1])),
			'newscount'=>delhtml(trim($news_info[2])),
			'ctime'=>date('Y-m-d')
		));
	}
	return;
}


/*
**����:��ȡ�����������µ�URL
**����:��ҳ��url
**
** By Laurence.Chen 2014��1��27��
** ������
*/
function get_m_proarea($url){
	//echo $url;exit;
	$clurl = get_page_url($url);
	$clurl->noReturn();
	$clurl->cut('<div class="title_box">','</div class="title_box">');
	$content = $clurl->getContent();
	$content = iconv("gb2312","UTF-8",$content);
	//echo $content;exit;
	$member_proarea = array();
	preg_match_all("'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx",$content,$reg);//��ȡ��Ա��Ʒ���Ƶ�����
	$cont=count($reg[2])-1;
		unset($reg[2][0]);//ȥ����һ��Ԫ��
		unset($reg[2][1]);//ȥ���ڶ���Ԫ��
		if(count($reg[2])!=1){
			unset($reg[2][$cont]);//ȥ�����һ��Ԫ��
		}
	//print_r($reg[2]);exit;
	return $reg[2];
}

/*
**����:��ȡ��Աÿ����Ʒ��Ϣ
**����:��ҳ��ַ
**
**By Laurence 2014��1��27��
**������
*/
function get_m_pro_info($url){
	$clurl = get_page_url($url);
	$clurl->noReturn();
	$clurl->cut('<div class="works-catalogInfo-space">','</div><div class="clear"></div>');
	$content = $clurl->getContent();
	$content = iconv("gb2312","UTF-8",$content);
	//echo $content;exit;
	$member_proimg = array();
	$member_proname	= array();
	preg_match_all('/<span class=\"title\" id=\"(\d+)">(.*)<\/span>/isU',$content,$title);//ƥ����Ʒ����
	showMsg('<br/><br/>��Ʒ���⣺'.$title[2][0]);
	preg_match_all('/<span id=\"bakup\">(.*)<\/span>/isU',$content,$conet);//ƥ����Ʒ˵��
	showMsg('��Ʒ���ܣ�'.$conet[1][0]);
	///<A href=.*pageNO=(.*?)>ĩҳ<\/a>/iU
	preg_match_all('/��һҳ<\/a>.*<A href=(.*?)>ĩҳ<\/a>/m',$content,$nextpage);//ƥ���Ƿ�����һҳ/pageNO=(\d)>/is
	//print_r($nextpage[1]); exit;
	$p = explode('pageNO=',$nextpage[1][0]);
	//print_r($p);exit;
	showMsg('��ƷͼƬҳ����'.$p[1].'&nbsp;&nbsp;ҳ');
	$page_url=array();
	if($nextpage[1]){
		for($page=1;$page<=$p[1];$page++){
			$page_url[$page]="http://www.china-designer.com/home/".$p[0]."pageNO=".$page;
		}
	}
	else{
			$page_url[]=$url;
	}
	showMsg("��ʼץȡÿҳ��Ʒ��Ϣ��url����......................");
	$pro_url_info_one_url=array();
	foreach($page_url as $p_url){
		$m_pro_info_array_url=get_m_pro_urllist($p_url);
		$pro_url_info_one_url=array_merge($pro_url_info_one_url,$m_pro_info_array_url);
		//break;
	}
	showMsg("����ļ����µ�ǰ��url����ץȡ���......................");
	//print_r($pro_url_info_one_url);print_r($m_pro_info_array_url);exit;
	$member_proarea[0]=$title[2];
	$member_proarea[1]=$conet[1];
	$member_proarea[2]=$pro_url_info_one_url;
	return $member_proarea;
}


/*
**����:��ȡ��Ʒ��Ϣ����һҳ������
**����:��ҳ��ַ��URL
**
**
**By Laurence.Chen 2014��1��27��
**������
*/
function get_m_pro_urllist($url){
	$url = get_page_url($url);
	$url->noReturn();
	$url->cut('works-catalogInfo-space','</div><div class="clear"></div>');
	$content = $url->getContent();
	$content = iconv("gb2312","UTF-8",$content);
	//echo $content;exit;
	preg_match_all("/<td align=\"left\"><a href=\"(.*?)\" target=_blank id=\"(.*?)\">(.*?)<\/a>/is",$content,$reg);
	//print_r($reg[1]);exit;
	return $reg[1];
}

/*
**����:ץȡ������Ϣ
**
**By Laurence.Chen 2014��2��8��
**������
*/
function get_news_list($url){
	$clurl = get_page_url($url);
	$clurl->noReturn();
	$clurl->cut('<div class="white-space maxWidth1">','</div><div class="clear"></div>');
	$content = $clurl->getContent();
	$content = iconv("gb2312","UTF-8",$content);
	//echo $content; exit;
	$m_newslist_url=array();
	preg_match_all('/<A href=(.*?)>(.*?)<\/a>/s',$content,$nextpage);  //ƥ���Ƿ�����һҳ/pageNO=(\d)>/is
	//ȥ���ظ���url
	if(count($nextpage[1])>2){
		unset($nextpage[1][count($nextpage[1])-1]);
		unset($nextpage[1][count($nextpage[1])-1]);
	}
	//print_r($nextpage[1]);
	preg_match_all("/<a href=\"(.*?)\"/is",$content,$newurl);//ƥ�����±����ϵ�����
	$newurl=array_unique($newurl[1]);
	$m_newslist_url=array_merge($m_newslist_url,$newurl);
	if($nextpage[1]){
		foreach($nextpage[1] as $key=>$nexturl){
			$newsurl="http://www.china-designer.com/home/".$nexturl;
			$next_news_url=get_news_next($newsurl);
			$m_newslist_url=array_merge($m_newslist_url,$next_news_url);
		}
	}
	$m_newslist_url=array_unique($m_newslist_url);
	//ȡ���������µ���ϸҳ��url
	//print_r($m_newslist_url);exit;
	return $m_newslist_url;
}
/*
**����:��ȡ���·�ҳ��һҳ����Ϣ
**
**
**By Laurence.Chen 2014��2��8��
**������
*/
function get_news_next($url){
	$url = get_page_url($url);
	$url->noReturn();
	$url->cut('<div class="white-space maxWidth1">','</div><div class="clear"></div>');
	$content = $url->getContent();
	$content = iconv("gb2312","UTF-8",$content);
	//echo $url;
	//echo $content;exit;
	preg_match_all("/<a href=\"(.*?)\"/is",$content,$newurl);//ƥ�����±����ϵ�����
	$newurl=array_unique($newurl[1]);
	return $newurl;
}
/*
**����:ȡ�����µ���ϸ��Ϣ
**
**
**By Laurence.Chen 2014��2��8��
**������
*/
function get_news_info($url){
	$url = get_page_url($url);
	$url->noReturn();
	$url->cut('<div class="article-show">','</div><div class="clear"></div>');
	$content = $url->getContent();
	$content = iconv("gb2312","UTF-8",$content);
	//echo $url;
	//echo $content;exit;
	preg_match_all("/<h3>(.*)<\/h3>/U",$content,$newtitle);//ƥ�����±���
	//print_r($newtitle[1][0]);
	showMsg("���±��⣺".$newtitle[1][0]);
	preg_match_all("/<p class=\"info\"> ���:(.*\s)/U",$content,$newtype);//ƥ���������
	//print_r($newtype[1][0]);
	showMsg("�������ͣ�".$newtype[1][0]);
	preg_match_all("/<div class=\"content\">(.*?)<center>/is",$content,$newcount);//ƥ����������
	//print_r($newcount[1]);
	$news_info[0]=$newtitle[1][0];
	$news_info[1]=$newtype[1][0];
	$news_info[2]=$newcount[1][0];
	//print_r($news_info);
	return $news_info;
}

/*************************�������������ǹ��õķ�����������*************************************************/
/**
***����: ����·��Ŀ¼
***����: �ڵ�ǰ��Ŀ�е�·��
***
***��ע�⡿�����dir�еĸ�ʽ: ��Ҫ�����ļ��е��������:{��Сд��ĸ+_+����}
**/
function mkdir_path($path){
	//$path =dirname($path);
	$dirs = split('[/\\]',$path);
	$temp = dirname(__FILE__);
	//print_r($dirs);exit;
	foreach($dirs as $dirname){
		if($dirname){
			if(!file_exists($temp.'/'.$dirname)){
				mkdir($temp.'/'.$dirname);
			}
			$temp.='/'.$dirname;
			//echo $temp."<br/>";
		}
	}
}

/**
***����: �����ļ�Ŀ¼�ķ���
***����: �ڵ�ǰ��Ŀ�е�·��
***
***��ע�⡿�����dir�еĸ�ʽ: ��Ҫ�����ļ��е��������:{��Сд��ĸ+_+����}
**/
function makeDirs($dirs='',$mode='0777'){
	$dirs=str_replace('\\','/',trim($dirs));
	if (!empty($dirs) && !file_exists($dirs)){
		makeDirs(dirname($dirs));//�ص�
		mkdir($dirs,$mode) or showErrMsg ('����Ŀ¼'.$dirs.'ʧ��,�볢���ֶ�����!');
	}
}

/**
**����:������ͨ��Ϣ��ʽ������ַ���
**
**����:
**$masage:��Ҫ��ʾ�ַ�����Ϣ
**$separation:�Ƿ���Ҫ����,����ʾ��ʽ���о����ƫ��
*/
function showMsg($masage,$separation=false){
	global $is_show_msg;
	if($separation){
		echo '<div style="font-size:12px">'.$masage.'.............</div>';
	}else{
		if($is_show_msg) echo '<div style="margin:3px; margin-left:30px;font-size:12px">'.$masage.'.............</div>';
	}
}

/**
**����:���ش�����Ϣ��ʽ������ַ���
**
**����:
**	$masage:�ַ���
*/
function showErrMsg($masage){
	echo '<div style="color:#F00;font-size:12px">'.$masage.'................</div>';
}

/**
**����:����Զ���ļ����浽����
**
**����:
**	$url:�ļ�·��
**	$newfname:�µ��ļ�������
*/
function get_file($url,$newfname){
	 set_time_limit (24 * 60 * 60);
	 $destination_folder = 'images/resources/';//�ļ����ر���Ŀ¼
	 $newfname = $destination_folder . $newfname;
	 $file = fopen ($url, "r");
	 if ($file) {
	 $newf = fopen ($newfname, "wb");
	 if ($newf)
	 while(!feof($file)) {
		fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
	 }
	 }
	 if ($file) {
		fclose($file);
	 }
	 if ($newf) {
		fclose($newf);
	 }
}

 /*
 **����:���HTML��ǩ
 **
 **
 **By Laurence.Chen  2014��2��8��
 **������
 */
function delhtml($str){
	$st=-1; //��ʼ
	$et=-1; //����
	$stmp=array();
	$stmp[]="&nbsp;";
	$len=strlen($str);
	for($i=0;$i<$len;$i++){
	 $ss=substr($str,$i,1);
	 if(ord($ss)==60){ //ord("<")==60
	  $st=$i;
	 }
	 if(ord($ss)==62){ //ord(">")==62
	  $et=$i;
	  if($st!=-1){
	  $stmp[]=substr($str,$st,$et-$st+1);
	  }
	 }
	}
	$str=str_replace($stmp,"",$str);
	$str=preg_replace("'<!--(.*?)-->'is","",$str);
	return trim($str);
}
?>