-- bot.lista definition

CREATE TABLE `lista` (
  `lista_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) NOT NULL,
  `confirma` tinyint(4) DEFAULT '0',
  `poder` int(11) DEFAULT NULL,
  `data_confirma` date DEFAULT NULL,
  PRIMARY KEY (`lista_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4;

-- bot.enquete definition

CREATE TABLE `enquete` (
  `opcao_id` int(11) NOT NULL AUTO_INCREMENT,
  `enquete_id` int(11) NOT NULL,
  `opcao` varchar(100) NOT NULL,
  PRIMARY KEY (`opcao_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

-- bot.chat_log definition

CREATE TABLE `chat_log` (
  `chat_id` int(11) NOT NULL AUTO_INCREMENT,
  `lista_id` int(11) DEFAULT NULL,
  `mensagem` text,
  `data_hora` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=906 DEFAULT CHARSET=utf8mb4;

-- bot.openai_log definition

CREATE TABLE `openai_log` (
  `openai_id` int(11) NOT NULL AUTO_INCREMENT,
  `lista_id` int(11) NOT NULL,
  `pergunta` text,
  `resposta` text,
  `data_hora` datetime DEFAULT NULL,
  PRIMARY KEY (`openai_id`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4;

-- bot.param definition

CREATE TABLE `param` (
  `key` varchar(100) NOT NULL,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- bot.reply definition

CREATE TABLE `reply` (
  `reply_id` int(11) NOT NULL AUTO_INCREMENT,
  `chave` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `texto` text NOT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`reply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4;

-- bot.voto_log definition

CREATE TABLE `voto_log` (
  `voto_id` int(11) NOT NULL AUTO_INCREMENT,
  `lista_id` int(11) NOT NULL,
  `opcao_id` int(11) NOT NULL,
  `enquete_id` int(11) NOT NULL,
  PRIMARY KEY (`voto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;