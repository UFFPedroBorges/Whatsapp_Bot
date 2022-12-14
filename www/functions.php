<?php
 
namespace functions { 

	//Function strposa:
	function stripos_somematch($haystack, $needles=array()) {
		$chr = array();
		foreach($needles as $needle) {
			$res = stripos($haystack, $needle);
			if ($res !== false) 
				return true;
		}
		return false;
	}
	
	function stripos_allmatch($haystack, $needles=array()) {
		$chr = array();
		foreach($needles as $needle) {
			$res = stripos($haystack, $needle);
			if ($res !== false) 
				$chr[$needle] = true;
			else 
				$chr[$needle] = false;
		}
		foreach($chr as $i => $v) {
			if ($v == false) return false;
		}
		return true;
	}
	
	function save_message($message, $lista_id) {
		require 'connect.php';
		mysqli_set_charset($conn,'utf8');
		
		$message = str_replace(["\r", "\n"], "<br>", $message);
		
		$query = "SELECT mensagem FROM chat_log WHERE lista_id = $lista_id AND mensagem = '$message' AND data_hora > DATE_SUB(NOW(), INTERVAL 1 MINUTE);";
		$result = mysqli_query($conn, $query);
		if(mysqli_affected_rows($conn) > 0)
			$fetch = mysqli_fetch_row($result);
		$check = isset($fetch[0]) ? $fetch[0] : '';
		
		if (!$check) {
			$query = "INSERT INTO chat_log (lista_id, mensagem, data_hora) VALUES ($lista_id, '$message', NOW());";
			mysqli_query($conn, $query);
		}
	
		return;
	}
	
	function openai_reply($sender, $message, $lista_id) {
		
		require 'connect.php';
		mysqli_set_charset($conn,'utf8');
		
		$query = "SELECT openai_id FROM openai_log WHERE pergunta = '$message' AND date_format(data_hora,'%d %m %Y %H %i') = date_format(NOW(),'%d %m %Y %H %i') AND lista_id = $lista_id;";
		$result = mysqli_query($conn, $query);
		$fetch = mysqli_fetch_row($result);
		$openai_id = isset($fetch[0]) ? $fetch[0] : '';
		if ($openai_id) {
			$wait = 0;
			do {
				sleep(0.01);
				
				$query = "SELECT resposta FROM openai_log WHERE openai_id = $openai_id;";
				$result = mysqli_query($conn, $query);
				$fetch = mysqli_fetch_row($result);
				$resposta = isset($fetch[0]) ? $fetch[0] : '';
				
				$wait += 1;
			} while (!$resposta OR $wait < 1000);
			$return = $resposta;
		}
		else {
			$query = "INSERT INTO openai_log (lista_id, pergunta, data_hora) VALUES ($lista_id, '$message', NOW());";
			mysqli_query($conn, $query);
			
			$query = "SELECT MAX(openai_id) FROM openai_log WHERE lista_id = $lista_id;";
			$result = mysqli_query($conn, $query);
			$fetch = mysqli_fetch_row($result);
			$log_id = isset($fetch[0]) ? $fetch[0] : '';
			
			$query = "SELECT value FROM param WHERE `key` = 'OPENAI_API_KEY';";
			$result = mysqli_query($conn, $query);
			$fetch = mysqli_fetch_row($result);
			$key = isset($fetch[0]) ? $fetch[0] : '';
			
			$convidados = "Lista de convidados n??o confirmados a comparecer: ";
			
			$query = "SELECT nome FROM lista WHERE confirma = 0;";
			$result = mysqli_query($conn, $query);
			$fetch = mysqli_fetch_row($result);
			while($fetch) {
				$convidados .= "$fetch[0], ";
				$fetch = mysqli_fetch_row($result);
			}
			$convidados .= "\nLista somente dos convidados confirmados a comparecer: ";
			
			$query = "SELECT nome FROM lista WHERE confirma >= 1;";
			$result = mysqli_query($conn, $query);
			$fetch = mysqli_fetch_row($result);
			while($fetch) {
				$convidados .= "$fetch[0], ";
				$fetch = mysqli_fetch_row($result);
			}
			
			//Get Context
			$query = "SELECT CASE WHEN cl.lista_id = 0 THEN 'Floquinho' ELSE nome END, mensagem FROM chat_log cl LEFT JOIN lista l ON cl.lista_id = l.lista_id ORDER BY data_hora DESC LIMIT 1, 6;";
			$result = mysqli_query($conn, $query);
			$fetch = mysqli_fetch_row($result);
			for ($i = 0; $fetch; $i++) {
				$context_sender[$i] = $fetch[0];
				$context_message[$i] = $fetch[1];
				$context_message[$i] = str_replace('<br>',' ',$context_message[$i]);
				$fetch = mysqli_fetch_row($result);
			}

			$url = 'https://api.openai.com/v1/completions';
			$method = 'POST';
			$opts = [
			  "model" => "text-davinci-002",
			  //"prompt" => "Human: Hello.",
			  "prompt" =>  "A seguir, uma conversa com um assistente extrovertido que se chama Floquinho, ele se encontra dentro do grupo de Whatsapp do evento.\n\nA confraterniza????o dos formandos de 2013 ser?? um evento para reunir todos os amigos que estudaram juntos no colegial. O evento acontecer?? no dia 1?? de outubro e contar?? com m??sica, dan??a, churrasco e bebidas.\n\nEndere??o do evento ?? Rua Sem Sa??da, numero 0.\n\nO evento come??ar?? as 16 horas sem hor??rio de t??rmino!\n\nTeremos na festa churrasco com churrasqueiro: Alcatra, contra-file, lingui??a e p??o de alho. Bebidas n??o alco??licas: ??gua com g??s, refrigerante, suco e energ??tico. Bebidas alco??licas: Cerveja, Vodka, Cacha??a e Gin.\n\nNecess??rio levar para o evento/festa somente a sua presen??a!\n\nO valor individual do evento ?? 90 reais. N??o ser?? aceito valor parcial. Chave Pix para pagamento ?? o email *XXX*. N??o esque??a de informar ?? organiza????o do evento sobre o seu pagamento.\n\nFloquinho trata somente de assunto relacionados a festa e os seus convidados.\n\nFloquinho fica triste quando algu??m ofende ele.\n\nRegra 1: Quem n??o confirmar presen??a at?? o dia 29 de Setembro ficar?? de fora do evento. N??o ser?? aceito a presen??a de quem n??o confirmou.\nRegra 2: Proibido discutir politica no grupo de WhatsApp do evento. Os forenses que insistirem diversas vezes poder??o ser expulsos do grupo.\nRegra 3: Proibido namorado(a) e outros tipos de companhias que estejam fora da lista de convidados.\n\nSegue lista dos principais comandos:\n*.endereco*, *.data* ou *.hora* = Te informo o endere??o, dia e hor??rio do evento.\n*.lista* = Te mostro a lista de convidados e confirmados no evento.\n*.confirma* = Confirmo seu nome na lista de presen??a.\n*.regra* = Te informo as regras do evento.\n*.valor* = Te informo o valor do evento e informa????es de pagamento.\n*.playlist* = Compartilharei com voc?? a playlist oficial do evento.\n*.buffet* = Te informo as comidas e bebidas que haver?? no evento.\n*.bebida* = Voc?? poder?? votar no seu tipo de bebida principal.\n*.cerveja* = Voc?? poder?? votar na sua marca de cerveja favorita.\n\n$convidados\n\nHist??rico ultimas mensagens:\n$context_sender[0]: $context_message[0].\n$context_sender[1]: $context_message[1].\n$context_sender[2]: $context_message[2].\n$context_sender[3]: $context_message[3].\n$context_sender[4]: $context_message[4].\n$context_sender[5]: $context_message[5].\n\n$sender: $message.\n\nFloquinho:",
			  "temperature" => 0.9,
			  "max_tokens" =>  150,
			  "top_p" =>  1,
			  "frequency_penalty" =>  0,
			  "presence_penalty" =>  0.6
			  //"stop" =>  [" Human:"," AI:"]
			];

			$post_fields = json_encode($opts);
			
			$curl_info = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_POSTFIELDS => $post_fields,
				CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer $key"],
				CURLOPT_SSL_VERIFYPEER => 0,
			];
			
			$curl = curl_init();

			curl_setopt_array($curl, $curl_info);
			
			$reply = curl_exec($curl);
			
			if (stripos($reply, 'insufficient_quota')) {
				$return = 'Desculpe, n??o posso conversar agora.';
				
				$query = "DELETE openai_log WHERE openai_id = $log_id;";
				mysqli_query($conn, $query);
			}
			else {
				if($reply === false)
					$reply = 'Curl error: ' . curl_error($curl);
				else 
					$reply = json_decode($reply,true);
				
				$return = $reply["choices"][0]["text"];
				$return = trim(substr($return,1,strlen($return)));
				$return = str_replace(["\r", "\n"], "<br>", $return);
				
				$query = "UPDATE openai_log SET resposta = '$return' WHERE openai_id = $log_id;";
				mysqli_query($conn, $query);
			}
			
			curl_close($curl);
		}
		
		return $return;
	}
	
}
?>