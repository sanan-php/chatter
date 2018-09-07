<?php

namespace Chat\Core;

class Headers
{
	public static function fileTransfer($fileName) : void
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize(Paths::DL_PATH . $fileName));
	}
	
	public static function webSocket(string $key)
	{
		$hash = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		header('HTTP/1.1 101 Web Socket Protocol Handshake');
		header('Upgrade: websocket');
		header('Connection: Upgrade');
		header('Sec-WebSocket-Accept: '.$hash);
		header('WebSocket-Origin:'.HOST.':8081');
		header('Content-Type: application/json');
	}

	public function contentType(string $type, string $charset = 'utf-8')
	{
		header('Content-Type: ' . $type . '; charset=' . $charset);
	}

	public function notFound()
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	}

	public function forbidden()
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
	}

	public function conflict()
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 409 Conflict');
	}

	public function redirect(string $url)
	{
		header('Location: ' . $url);
	}

	public static function set()
	{
		return new self();
	}
}
