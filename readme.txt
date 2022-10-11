Chatbot desenvolvido para auxiliar o grupo de whatsapp de um churrasco. 
Está aplicação utiliza recursos que estáo disponiveis somente na licença premium do aplicativo WhatsAuto (https://play.google.com/store/apps/details?id=com.guibais.whatsauto&hl=pt_BR&gl=US)

ATENÇÃO: Necessário se conectar ao aplicativo WhatsAuto e configurar a opção Server/Servidor para que se comunique com está aplicação.

Está aplicação está sobre a licença lgpl.


Informações gerais de desenvolvimento:

Linguagens utilizadas: PHP, JSON, SQL

API utilizadas: OpenAI API (https://openai.com/)

Testado com VertrigoServ utilizando as seguintes ferramentas:
Apache 2.4.38
PHP 7.4.30
MySQL 5.7.25

========================= MINI TUTORIAL ==========================

*Tabela "lista". Onde é cadastrado os convidados no qual o chatbot reconhecerá, nestá aplicação o cadastro é feito direto pelo banco de dados manualmente.
Detalhes importantes: O nome do integrante da lista precisa ser igual ao da lista telefônica do celular ao qual é utilizado o bot.

*Tabela "param". Onde fica registrado valores de parametros salvos. Atualmente existem somente 3 parametros que dizem respeito ao OpenAI API que são:
OPENAI_API_KEY -> Sua chave para utilização da sua conta OpenAI.
OPENAI_USE_DAY -> Quantidade de utilização da API por usuário. (Limitador para evitar que usuários façam spam e consumam todos seus créditos)
OPENAI_USE_LEVEL -> Nível de poder que o usuário necessita ter na tabela "lista" para que não seja limitado pelo OPENAI_USE_DAY.

*Tabela "reply". Onde é cadastrado as respostas automáticas comuns do bot. 
Campo "chave" -> Palavra chave que será filtrada para que a resposta seja dada. Permitido utilizar varias palavras chaves separadas por virgula.
Campo "texto" -> Resposta que será dada pelo Bot quando a palavra chave for atendida. Permitido utilizar parametros como: {name} e {phone} para trazer o nome ou telefone do usuário.
Campo "tipo" -> Tipo 1 = Palavra chave precisa coincindir completamente. Tipo 2 = Pode coincidir somente uma das palavras chaves. Tipo 3 = Precisa coincidir todas as palavras chaves.
