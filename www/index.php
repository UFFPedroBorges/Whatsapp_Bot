<?php 
header("Content-type: text/html; charset=utf-8");

include 'functions.php';
use function functions\{stripos_allmatch, stripos_somematch, save_message, openai_reply};

//Parameters
if (!empty($_POST["app"])) $app = trim($_POST["app"]); else $app = '';
if (!empty($_POST["sender"])) $sender = trim($_POST["sender"]); else $sender = '';
if (!empty($_POST["message"])) $message = trim($_POST["message"]); else $message = '';
if (!empty($_POST["phone"])) $phone = trim($_POST["phone"]); else $phone = '';
if (!empty($_POST["group_name"])) $group_name = trim($_POST["group_name"]); else $group_name = '';

$bot_name = 'Cenequinho';

require_once 'connect.php';
mysqli_set_charset($conn,'utf8');

//Get lista_id and poder
$query = "SELECT lista_id, poder FROM lista WHERE telefone = '$phone' OR nome = '$sender';";
$result = mysqli_query($conn, $query);
$fetch = mysqli_fetch_row($result);
$lista_id = isset($fetch[0]) ? $fetch[0] : '';
$poder = isset($fetch[1]) ? $fetch[1] : '';

//Save history
save_message($message, $lista_id);

//OpenAI
If (substr($message,0,10) == 'Cenequinho') {
	
	if ($lista_id) {
		$query = "SELECT value FROM param WHERE `key` IN ('OPENAI_USE_DAY','OPENAI_USE_LEVEL') ORDER BY `key` ASC;";
		$result = mysqli_query($conn, $query);
		$fetch = mysqli_fetch_row($result);
		$OPENAI_USE_DAY = isset($fetch[0]) ? $fetch[0] : '';
		$OPENAI_USE_LEVEL = isset($fetch[1]) ? $fetch[1] : '';
		
		if ($OPENAI_USE_LEVEL > $poder) {
			$query = "SELECT COUNT(*) FROM openai_log WHERE lista_id = $lista_id AND date_format(data_hora,'%d %M %Y') = date_format(NOW(),'%d %M %Y');";
			$result = mysqli_query($conn, $query);
			$fetch = mysqli_fetch_row($result);
			$count = isset($fetch[0]) ? $fetch[0] : '';
			
			if ($count < $OPENAI_USE_DAY) {
				$reply = openai_reply($sender,$message,$lista_id);
			}
			else {
				$reply = "$sender já conversei muito contigo hoje. Amanhã a gente troca mais ideias.\nVocê ainda pode utilizar meus comandos para obter informações sobre o evento. Utilize o comando *.comando*.";
			}
		}
		else {
			$reply = openai_reply($sender,$message,$lista_id);
		}
	}
	else {
		$reply = "$sender não encontrei seu nome na lista de convidados. Eu só troco ideia com os convidados da festa. Não fique chateado, pode ser um erro. Entre em contato com alguém da organização para resolver o seu problema. Utilize o comando *.staff*.";
	}
}
//Command: .pago
Else If (substr($message,0,5) == '.pago' && $poder >= 2) {
	$param = trim(substr($message,6,strlen($message)));

	$query = "SELECT lista_id, confirma FROM lista WHERE nome = '$param';";
	$result = mysqli_query($conn, $query);
	if(mysqli_affected_rows($conn) > 0) {
		$fetch = mysqli_fetch_row($result);
		$lista_id2 = isset($fetch[0]) ? $fetch[0] : '';
		$confirma = isset($fetch[1]) ? $fetch[1] : '';
	}

	if (isset($lista_id2)) {
		if ($confirma == '2') {
			$reply = "Pagamento já foi registrado para $param.";
		}
		else {
			$query = "UPDATE lista SET confirma = 2 WHERE lista_id = $lista_id2;";
			mysqli_query($conn, $query);
			
			$reply = "Registrado com sucesso o pagamento de $param.";
		}
	}
	Else {
		$reply = "Erro: $param não encontrado.";
	}
}
//Command: .addoption
Else If (substr($message,0,10) == '.addoption' && $poder >= 3) {
	$param1 = trim(substr($message,11,1));
	$param2 = trim(substr($message,12,strlen($message)));

	$query = "SELECT enquete_id FROM enquete WHERE enquete_id = $param1;";
	$result = mysqli_query($conn, $query);
	if(mysqli_affected_rows($conn) > 0) {
		$fetch = mysqli_fetch_row($result);
		$check = isset($fetch[0]) ? $fetch[0] : '';
	}
	
	if (isset($check)) {
		$query = "INSERT INTO enquete (enquete_id, opcao) VALUES ($param1, '$param2')";
		mysqli_query($conn, $query);
		
		$reply = "Nova opção adicionada com sucesso.";
	}
	Else {
		$reply = "Erro: Enquete não identificada.";
	}
}
//Command: .bebida
Else If (strtolower($message) == '.bebida') {
	$reply = "Votação tipos de bebida:";
	
	$query = "SELECT A.opcao, IFNULL(B.votos,0) as votos FROM (SELECT opcao_id, opcao FROM enquete WHERE enquete_id = 2) A LEFT JOIN (SELECT opcao_id, COUNT(*) as votos FROM voto_log VL INNER JOIN lista L ON VL.lista_id = L.lista_id WHERE confirma = 1 GROUP BY opcao_id) B ON A.opcao_id = B.opcao_id;";
	$result = mysqli_query($conn, $query);
	$fetch = mysqli_fetch_row($result);
	while($fetch) {
		foreach ($fetch as $index => $value) {
			if ($index == 0)
				$tipo = $value;
			if ($index == 1) {
				$votos = $value;
			}
		}
		$reply .= "\n-> $tipo - $votos voto(s)";
		$fetch = mysqli_fetch_row($result);
	}
	$reply .= "\n\nPara votar no seu tipo de bebida digite o comando .bebida junto com o tipo da bebida. Exemplo: *.bebida opção*\n";
	$reply .= "*Obs:* Todos os tipos de bebidas serão oferecidos no evento. Está enquete será utilizada para proporção das quantidades.";
}
Else If (substr($message,0,7) == '.bebida') {
	$param = trim(substr($message,8,strlen($message)));
	
	$query = "SELECT confirma FROM lista WHERE lista_id = $lista_id;";
	$result = mysqli_query($conn, $query);
	$fetch = mysqli_fetch_row($result);
	$confirma = isset($fetch[0]) ? $fetch[0] : '';

	if ($confirma >= 1) {
		$query = "SELECT opcao_id, enquete_id FROM enquete WHERE opcao = '$param';";
		$result = mysqli_query($conn, $query);
		$fetch = mysqli_fetch_row($result);
		$opcao_id = isset($fetch[0]) ? $fetch[0] : '';
		$enquete_id = isset($fetch[1]) ? $fetch[1] : '';
		
		if ($opcao_id) {
			$query = "SELECT voto_id, opcao FROM voto_log VL INNER JOIN enquete E ON VL.opcao_id = E.opcao_id WHERE VL.enquete_id = 2 AND lista_id = $lista_id;";
			$result = mysqli_query($conn, $query);
			$fetch = mysqli_fetch_row($result);
			$voto_id = isset($fetch[0]) ? $fetch[0] : '';
			$voto_anterior = isset($fetch[1]) ? $fetch[1] : '';
			
			$query = "INSERT INTO voto_log (opcao_id, enquete_id, lista_id) VALUES ($opcao_id, $enquete_id, $lista_id)";
			mysqli_query($conn, $query);
			
			$reply = "$sender computei com sucesso seu voto para *$param*. 🍻";
			
			if ($voto_id) {
				$query = "DELETE FROM voto_log WHERE voto_id = $voto_id;";
				mysqli_query($conn, $query);
				
				$reply .= "\nSeu voto anterior para *$voto_anterior* foi descartado.";
			}
		}
		else {
			$reply = "$sender desculpe, não entendi bem a sua escolha. Favor digitar o nome do tipo da sua bebida corretamente. (Exemplo: *.bebida cerveja*)";
		}
	}
	else {
		$reply = "$sender desculpe, mas somente quem confirmou presença no evento pode votar.\nPara você confirmar a sua presença utilize o comando *.confirma*.";
	}
}
//Command: .cerveja
Else If (strtolower($message) == '.cerveja') {
	$reply = "Votação marcas de cerveja:";
	
	$query = "SELECT A.opcao, IFNULL(B.votos,0) as votos FROM (SELECT opcao_id, opcao FROM enquete WHERE enquete_id = 1) A LEFT JOIN (SELECT opcao_id, COUNT(*) as votos FROM voto_log VL INNER JOIN lista L ON VL.lista_id = L.lista_id WHERE confirma = 1 GROUP BY opcao_id) B ON A.opcao_id = B.opcao_id;";
	$result = mysqli_query($conn, $query);
	$fetch = mysqli_fetch_row($result);
	while($fetch) {
		foreach ($fetch as $index => $value) {
			if ($index == 0)
				$marca = $value;
			if ($index == 1) {
				$votos = $value;
			}
		}
		$reply .= "\n-> $marca - $votos voto(s)";
		$fetch = mysqli_fetch_row($result);
	}
	$reply .= "\n\nSua marca não está na lista? Entre em contato com a organização *(.staff)*.\n";
	$reply .= "Para votar na sua marca favorita digite o comando .cerveja junto com sua marca. Exemplo: *.cerveja marca*\n";
	$reply .= "*Obs:* O evento não garante servir todas as marcas votadas, estando sujeito a disponibilidade do mercado.";
}
Else If (substr($message,0,8) == '.cerveja') {
	$param = trim(substr($message,8,strlen($message)));
	
	$query = "SELECT confirma FROM lista WHERE lista_id = $lista_id;";
	$result = mysqli_query($conn, $query);
	$fetch = mysqli_fetch_row($result);
	$confirma = isset($fetch[0]) ? $fetch[0] : '';

	if ($confirma >= 1) {
		$query = "SELECT opcao_id, enquete_id FROM enquete WHERE opcao = '$param';";
		$result = mysqli_query($conn, $query);
		$fetch = mysqli_fetch_row($result);
		$opcao_id = isset($fetch[0]) ? $fetch[0] : '';
		$enquete_id = isset($fetch[1]) ? $fetch[1] : '';
		
		if ($opcao_id) {
			$query = "SELECT voto_id, opcao FROM voto_log VL INNER JOIN enquete E ON VL.opcao_id = E.opcao_id WHERE VL.enquete_id = 1 AND lista_id = $lista_id;";
			$result = mysqli_query($conn, $query);
			$fetch = mysqli_fetch_row($result);
			$voto_id = isset($fetch[0]) ? $fetch[0] : '';
			$voto_anterior = isset($fetch[1]) ? $fetch[1] : '';
			
			$query = "INSERT INTO voto_log (opcao_id, enquete_id, lista_id) VALUES ($opcao_id, $enquete_id, $lista_id)";
			mysqli_query($conn, $query);
			
			$reply = "$sender computei com sucesso seu voto para a cerveja *$param*. 🍻";
			
			if ($voto_id) {
				$query = "DELETE FROM voto_log WHERE voto_id = $voto_id;";
				mysqli_query($conn, $query);
				
				$reply .= "\nSeu voto anterior para *$voto_anterior* foi descartado.";
			}
		}
		else {
			$reply = "$sender desculpe, não entendi bem a sua escolha. Favor digitar o nome da marca da sua cerveja corretamente. (Exemplo: *.cerveja brahma*)";
		}
	}
	else {
		$reply = "$sender desculpe, mas somente quem confirmou presença no evento pode votar.\nPara você confirmar a sua presença utilize o comando *.confirma*.";
	}
}
//Command: .lista / .lista2
Else If (strtolower($message) == '.lista' || strtolower($message) == '.lista2') {
	
	If (strtolower($message) == '.lista') {
		$query = "SELECT nome, confirma FROM lista ORDER BY nome ASC;";
		$reply = "Lista de Convidados:\n";
	} else {
		$query = "SELECT nome, confirma FROM lista WHERE confirma >= 1 ORDER BY nome ASC;";
		$reply = "Lista de Confirmados:\n";
	}
	$result = mysqli_query($conn, $query);
	$fetch = mysqli_fetch_row($result);
	while($fetch) {
		foreach ($fetch as $index => $value) {
			if ($index == 0)
				$name = $value;
			if ($index == 1) {
				if ($value == '1') $confirmado = '*(Confirmado)*';
				else if ($value == '2') $confirmado = '*(Pago)*';
				else $confirmado = '';
			}
		}
		$reply .= "-> $name $confirmado\n";
		$fetch = mysqli_fetch_row($result);
	}
	$reply .= "\nUtilize *.confirma* para confirmar sua presença.\nUtilize *.retira* para retirar sua confirmação.\n";
	If (strtolower($message) == '.lista') $reply .= "Utilize *.lista2* para ver somente a lista de confirmados.\nSentiu falta de algum colega que está fora da lista? Envie o contato dele para a staff, comando *.staff*.";
	Else $reply .= "Utilize *.lista* para ver a lista completa de convidados.";
}
//Command: .confirma / .retira
Else If (strtolower($message) == '.confirma' || strtolower($message) == '.retira') {

	if ($lista_id) {
		$query = "SELECT confirma FROM lista WHERE lista_id = '$lista_id';";
		$result = mysqli_query($conn, $query);
		$fetch = mysqli_fetch_row($result);
		$confirma = isset($fetch[0]) ? $fetch[0] : '';
		
		if ($confirma == '2') {
			$reply = "$sender seu pagamento já foi identificado, logo você está confirmado.";
		}
		else if (strtolower($message) == '.confirma')
			if ($confirma == '0') {
				$query = "UPDATE lista SET confirma = 1, data_confirma = NOW() WHERE lista_id = '$lista_id';";
				mysqli_query($conn, $query);
				
				$reply = "$sender anotei com sucesso sua confirmação no evento. 😃";
			}
			else $reply = "$sender você já se encontra confirmado.";
		else {
			if ($confirma == '1') {
				$query = "UPDATE lista SET confirma = 0 WHERE lista_id = '$lista_id';";
				mysqli_query($conn, $query);
				
				$reply = "Poxa que pena $sender que você não poderá mais participar do evento conosco! 😞\nJá registrei a retirada de sua confirmação da lista.";
			}
			else $reply = "$sender você já se encontra sem confirmação.";
		}
	}
	else {
		$reply = "$sender não encontrei seu nome na lista de convidados. Não fique chateado, pode ser um erro. Entre em contato com alguém da organização para resolver o seu problema. Utilize o comando *.staff*.";
	}
}
else {

	//Exact Match Incoming (Type 1)
	$query = "SELECT reply_id FROM reply WHERE chave = '$message' AND type = 1;";
	$result = mysqli_query($conn, $query);
	$fetch = mysqli_fetch_row($result);
	$reply_id = isset($fetch[0]) ? $fetch[0] : '';

	//Contains Incoming (Type 2 or 3)
	if (!$reply_id) {
		$query = "SELECT MIN(reply_id), chave, type FROM reply WHERE type in (2,3) GROUP BY chave ORDER BY type ASC;";
		$result = mysqli_query($conn, $query);
		$fetch = mysqli_fetch_row($result);
		$check = false;
		while($fetch && $check == false) {
			foreach ($fetch as $index => $value) {
				if ($index == 0)
					$num = $value;
				if ($index == 1)
					$search = explode(',',$value);
				if ($index == 2)
					$type = $value;
			}
			if ($type == 2)
				$check = stripos_allmatch($message, $search);
			else
				$check = stripos_somematch($message, $search);
			
			$fetch = mysqli_fetch_row($result);
		}
		if ($check == true) $reply_id = $num;
	}

	if ($reply_id) {
		//Random Reply (if there is)
		$query = "SELECT reply_id, RAND() FROM reply WHERE chave = (SELECT chave FROM reply WHERE reply_id = $reply_id) ORDER BY RAND();";
		$result = mysqli_query($conn, $query);
		$fetch = mysqli_fetch_row($result);
		$reply_id = isset($fetch[0]) ? $fetch[0] : '';

		//Get Reply
		$query = "SELECT texto FROM reply WHERE reply_id = $reply_id;";
		$result = mysqli_query($conn, $query);
		$fetch = mysqli_fetch_row($result);
	}
	
	$reply = isset($fetch[0]) ? $fetch[0] : '';
}
if (isset($result) && mysqli_affected_rows($conn) > 0)
	mysqli_free_result($result);
mysqli_close($conn);

//Send Reply (or no)
if ($reply) {
	$response = array("reply" => "*$bot_name* 🤖🎉\n$reply");
	$response = json_encode($response,JSON_UNESCAPED_UNICODE);
	$response = str_replace('<br>','\n',$response);
	$response = str_replace('{name}',$sender,$response);
	$response = str_replace('{phone}',$phone,$response);
	
	save_message($reply, 0);
} else {
	$response = array("reply" => "");
	$response = json_encode($response);
}
echo $response;
?>