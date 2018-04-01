<?php
/** 
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configuraçções de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'pousadakatedra');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'pousadakatedra');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'db73544');

/** nome do host do MySQL */
define('DB_HOST', 'mysql.pousadakatedral.com.br');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando {@link http://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service}
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 			'764679692e40a5e12d18d911aca8c115516a6881ab5a66b25b926253a17b6978');
define('SECURE_AUTH_KEY', 	'70843920ea42dd4011841ba2c5d9febd2498fe1bba027697ef62199d9db5b256');
define('LOGGED_IN_KEY', 	'210f953f3131f9a3b3c1514447fc52f487578540cf220d347037f5aec7fe235c');
define('NONCE_KEY', 		'6ee754ab842bfda278ebf2cb804c69ff0acb2af63669da58ef3810630b19f460');
define('AUTH_SALT',			'8b6748af6c3778124796c138a1ed2d94efcfabb09c399828fc899b6c37e368f6');
define('SECURE_AUTH_SALT',	'42acc39a2c867006d6c97ea67f5aba5980f2491991f27766eb9aaca45acbba8c');
define('LOGGED_IN_SALT',	'f8e9194ba9e43b0faa1a0122494a84665ff659ab62131d477b474739764b3e45');
define('NONCE_SALT',		'b0ba251a7f38628e9d238f554662ba7c2952908c410ff989efb5190e12e27154');
/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente a
 * língua escolhida deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define ('WPLANG', 'pt_BR');

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto do WordPress para o diretório Wordpress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');
?>
