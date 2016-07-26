<?php

namespace Http\StreamWrapper;

use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class StreamWrapper
{
    /**
     * @var bool
     */
    private static $enabled = false;

    /**
     * @var ResponseInterface
     */
    private static $response;

    /**
     * @var HttpClient
     */
    private static $httpClient;

    /**
     * @param HttpClient $client
     */
    public static function enable(HttpClient $client)
    {
        if (self::$enabled) {
            self::disable();
        }

        stream_wrapper_unregister('http');
        stream_wrapper_register('http', __CLASS__, STREAM_IS_URL);

        stream_wrapper_unregister('https');
        stream_wrapper_register('https', __CLASS__, STREAM_IS_URL);

        self::$enabled = true;
        self::$httpClient = $client;
    }

    /**
     *
     */
    public static function disable()
    {
        stream_wrapper_restore('http');
        stream_wrapper_restore('https');

        self::$enabled = false;
    }

    /**
     * @return bool
     */
    public static function isEnabled()
    {
        return self::$enabled;
    }

    /**
     * This method is called immediately after the wrapper is initialized (f.e. by fopen() and file_get_contents()).
     *
     * @link http://www.php.net/manual/en/streamwrapper.stream-open.php
     *
     * @param string $path        Specifies the URL that was passed to the original function.
     * @param string $mode        The mode used to open the file, as detailed for fopen().
     * @param int    $options     Holds additional flags set by the streams API.
     * @param string $opened_path If the path is opened successfully, and STREAM_USE_PATH is set.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public static function stream_open($path, $mode, $options, &$opened_path)
    {
        // TODO Support POST requests or at least make sure they work
        $request = MessageFactoryDiscovery::find()->createRequest('GET', $path);

        self::$response = self::getHttpClient()->sendRequest($request);

        return true;
    }

    /**
     * Read from stream.
     *
     * @link http://www.php.net/manual/en/streamwrapper.stream-read.php
     *
     * @param int $count How many bytes of data from the current position should be returned.
     *
     * @return string If there are less than count bytes available, return as many as are available.
     *                If no more data is available, return either FALSE or an empty string.
     */
    public static function stream_read($count)
    {
        $ret = self::$response->getBody()->read($count);

        return $ret;
    }

    /**
     * Write to stream.
     *
     * @throws \BadMethodCallException If called, because this method is not applicable for this stream.
     *
     * @link http://www.php.net/manual/en/streamwrapper.stream-write.php
     *
     * @param string $data Should be stored into the underlying stream.
     *
     * @return int
     */
    public static function stream_write($data)
    {
        throw new \BadMethodCallException('No writing possible');
    }

    /**
     * Retrieve the current position of a stream.
     *
     * This method is called in response to fseek() to determine the current position.
     *
     * @link http://www.php.net/manual/en/streamwrapper.stream-tell.php
     *
     * @return int Should return the current position of the stream.
     */
    public static function stream_tell()
    {
        return self::$response->getBody()->tell();
    }

    /**
     * Tests for end-of-file on a file pointer.
     *
     * @link http://www.php.net/manual/en/streamwrapper.stream-eof.php
     *
     * @return bool Should return TRUE if the read/write position is at the end of the stream
     *              and if no more data is available to be read, or FALSE otherwise.
     */
    public static function stream_eof()
    {
        return self::$response->getBody()->eof();
    }

    /**
     * Retrieve information about a file resource.
     *
     * @link http://www.php.net/manual/en/streamwrapper.stream-stat.php
     *
     * @return array See stat().
     */
    public static function stream_stat()
    {
        return [];
    }

    /**
     * Retrieve information about a file resource.
     *
     * @link http://www.php.net/manual/en/streamwrapper.url-stat.php
     *
     * @return array See stat().
     */
    public static function url_stat($path, $flags)
    {
        return [];
    }

    /**
     * Seeks to specific location in a stream.
     *
     * @param int $offset The stream offset to seek to.
     * @param int $whence Possible values:
     *                    SEEK_SET - Set position equal to offset bytes.
     *                    SEEK_CUR - Set position to current location plus offset.
     *                    SEEK_END - Set position to end-of-file plus offset.
     *
     * @return bool Return TRUE if the position was updated, FALSE otherwise.
     */
    public static function stream_seek($offset, $whence)
    {
        return self::$response->getBody()->seek($offset, $whence);
    }

    /**
     * Change stream options.
     *
     * @link http://www.php.net/manual/en/streamwrapper.stream-metadata.php
     *
     * @param string $path   The file path or URL to set metadata.
     * @param int    $option One of the stream options.
     * @param mixed  $var    Value depending on the option.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public static function stream_metadata($path, $option, $var)
    {
        return false;
    }

    /**
     * @param HttpClient $client
     */
    private static function setHttpClient(HttpClient $client)
    {
        self::$httpClient = new PluginClient(
            $client,
            [
                // TODO add more plugins to copy default behavior
                new RedirectPlugin(),
            ]
        );
    }

    /**
     * @return HttpClient
     */
    public static function getHttpClient()
    {
        return self::$httpClient;
    }
}
