INSERT INTO bot.param
(`key`, value)
VALUES('OPENAI_API_KEY', '<OPENAI_KEY>');
INSERT INTO bot.param
(`key`, value)
VALUES('OPENAI_USE_DAY', '25');
INSERT INTO bot.param
(`key`, value)
VALUES('OPENAI_USE_LEVEL', '1');


INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(1, 1, 'Brahma');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(2, 1, 'Antartica');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(3, 1, 'Itaipava');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(4, 1, 'Skol');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(5, 1, 'Heineken');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(6, 1, 'Budweiser');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(7, 1, 'Lokal');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(8, 1, 'Amstel');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(9, 2, 'Refrigerante');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(10, 2, 'Suco');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(11, 2, 'Cerveja');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(12, 2, 'Vodka');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(13, 2, 'Cachaça');
INSERT INTO bot.enquete
(opcao_id, enquete_id, opcao)
VALUES(14, 2, 'Gin');


INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(1, '.regra', 'Regras do Evento e Grupo:<br>*1-* Quem não confirmar presença até o dia *29 de Setembro* ficará de fora do evento. Não será aceito a presença de quem não confirmou.<br><br>*2-* Proibido discutir politica no grupo de WhatsApp do evento.<br>Os forenses que insistirem diversas vezes poderão ser expulsos do grupo.<br><br>*3-* Proibido namorado(a) e outros tipos de companhias que estejam fora da lista de convidados.<br><br>Quaisquer dúvidas, entre em contato com a organização. Para saber quem pertence a organização utilize o comando *.staff*.', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(2, '.staff', 'Segue contato da organização do evento:<br>...', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(3, '.valor', 'O valor individual do evento é 90 reais.<br>Não será aceito valor parcial.<br>Chave Pix para pagamento é o email *XXX*.<br>Não esqueça de informar à organização do evento sobre o seu pagamento, para saber quem procurar utilize o comando *.staff*.', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(4, '.comando', ' Os comandos iniciam com ponto (.), segue lista dos principais comandos:<br>*.endereco*, *.data* ou *.hora* = Te informo o endereço, dia e horário do evento.<br>*.lista* = Te mostro a lista de convidados e confirmados no evento.<br>*.confirma* = Confirmo seu nome na lista de presença.<br>*.regra* = Te informo as regras do evento.<br>*.valor* = Te informo o valor do evento e informações de pagamento.<br>*.playlist* = Compartilharei com você a playlist oficial do evento.<br>*.buffet* = Te informo as comidas e bebidas que haverão no evento.<br>*.bebida* = Você poderá votar no seu tipo de bebida principal.<br>*.cerveja* = Você poderá votar na sua marca de cerveja favorita.', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(5, '.teste', 'Teste sucedido!', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(6, '.bot', 'Olá! Meu nome é Floquinho e eu sou o chatbot exclusivo do grupo da confraternização dos formados de 2013!<br>Utilize o comando *.comando* para conhecer os comandos disponíveis.<br>Eu também tenho a capacidade de entender *linguagem natural*, basta me enviar uma mensagem iniciada com meu nome, eu irei te responder da forma mais natural possível. Exemplos:<br>*Correto:* "Floquinho bom dia!"<br>*Errado:* "Bom dia Floquinho!"', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(7, '.endereco', 'Evento ocorrerá no sábado dia *01/10/2022*.<br>Início as *16 horas* sem horário de fim.<br>Endereço no link do Google Maps:<br>...', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(8, '.data', 'Evento ocorrerá no sábado dia *01/10/2022*.<br>Início as *16 horas* sem horário de fim.<br>Endereço no link do Google Maps:<br>...', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(9, '.hora', 'Evento ocorrerá no sábado dia *01/10/2022*.<br>Início as *16 horas* sem horário de fim.<br>Endereço no link do Google Maps:<br>...', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(10, '.playlist', 'Link da playlist oficial do evento: ... <br>Caso queira colaborar com a playlist do evento, acesse este link: ...', 1);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(11, 'sair,grupo', 'Poxa {name} não saia do grupo, todos vão sentir a sua falta! Que tal somente mutar o grupo? {name} lembre-se que todos aqui são seus amiguinhos do coração.', 2);
INSERT INTO bot.reply
(reply_id, chave, texto, `type`)
VALUES(12, 'bolsonaro, lula,petista,bolsominion, bozo,bonoro,esquerdista,bolsomito,lulinha,bozonaro,bolsonazi,comunista,socialista,nazista, ciro ', '{name} favor não trazer assuntos de politica no grupo. Maiores detalhes consultar o comando *.regra*.', 3);
