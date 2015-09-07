<?php
// Создаем подключение к серверу
// $db = mysql_connect ("servername","user","password"); 
// Выбираем БД
//mysql_select_db ("dbname",$db);

// Все загруженные файлы помещаются в эту папку
//$uploaddir = 'images/';
$imageKeeper = DIRECTORY_SEPARATOR ."html5-uploader" . DIRECTORY_SEPARATOR ."images" . DIRECTORY_SEPARATOR ;
$uploaddir = __DIR__ . DIRECTORY_SEPARATOR . 'images'. DIRECTORY_SEPARATOR;
if (!file_exists($uploaddir)) {
	mkdir($uploaddir);
}


// Вытаскиваем необходимые данные
$file = $_POST['value'];
$name = $_POST['name'];

// Получаем расширение файла
$getMime = explode('.', $name);
$mime = end($getMime);

// Выделим данные
$data = explode(',', $file);

// Декодируем данные, закодированные алгоритмом MIME base64
$encodedData = str_replace(' ','+',$data[1]);
$decodedData = base64_decode($encodedData);

// Вы можете использовать данное имя файла, или создать произвольное имя.
// Мы будем создавать произвольное имя!
$randomName = substr_replace(sha1(microtime(true)), '', 12).'.'.$mime;

// Создаем изображение на сервере
if(file_put_contents($uploaddir.$randomName, $decodedData)) {
	// Записываем данные изображения в БД
	//mysql_query ("INSERT INTO images (date,catalog,filename) VALUES (NOW(),'$uploaddir','$randomName')");
	// echo $randomName.":загружен успешно";

	$arr = array(
		'name' => $randomName,
		'link' => "http://" . $_SERVER['HTTP_HOST'] . $imageKeeper, // выводит localhost:8888
		'direct' => "http://" . $_SERVER['HTTP_HOST'] . $imageKeeper.$randomName,
		'PreviewBB' => "[url=" . $_SERVER['HTTP_HOST'] . $imageKeeper.$randomName ."][img]" . $_SERVER['HTTP_HOST'] . $imageKeeper.$randomName. "[/img][/url]",
		'PreviewHTML' => html_entity_decode ("<a href=". $_SERVER['HTTP_HOST'] . $imageKeeper.$randomName. " target='_blank'><img src=" . $_SERVER['HTTP_HOST'] . $imageKeeper.$randomName. " alt=".$name."></a>"),
		'directLink' => "http://" . $_SERVER['HTTP_HOST'] . $imageKeeper.$randomName,
		'imageBB' => "[img]" . $_SERVER['HTTP_HOST'] . $imageKeeper.$randomName. "[/img]",
		'imageHTML' => "<img src=" . $_SERVER['HTTP_HOST'] . $imageKeeper.$randomName. " alt=".$name.">"
		//'imageHTML' =>  html_entity_decode ('"<img src=" . $_SERVER['HTTP_HOST'] . $imageKeeper.$randomName. " alt=".$name.">"' )
	);

	echo json_encode($arr);
	
	/*echo json_decode([
   // 'success' => true,
    'files' => 123,
   // 'get' => $_GET,
   // 'post' => $_POST,
    //optional
    //'flowTotalSize' => isset($_FILES['file']) ? $_FILES['file']['size'] : $_GET['flowTotalSize'],
    //'flowIdentifier' => isset($_FILES['file']) ? $_FILES['file']['name'] . '-' . $_FILES['file']['size']
     //   : $_GET['flowIdentifier'],
    //'flowFilename' => isset($_FILES['file']) ? $_FILES['file']['name'] : $_GET['flowFilename'],
    //'flowRelativePath' => isset($_FILES['file']) ? $_FILES['file']['tmp_name'] : $_GET['flowRelativePath']
]);*/

}
else {
	// Показать сообщение об ошибке, если что-то пойдет не так.
	echo "Что-то пошло не так. Убедитесь, что файл не поврежден!";
}




?>