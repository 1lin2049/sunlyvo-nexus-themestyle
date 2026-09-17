<?php
/**
 * SunLyvo Nexus — 对象缓存安装器
 *
 * 负责生成/删除 wp-content/object-cache.php（WordPress drop-in）。
 * 配置存于 wp-content/slv-cache-config.php。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Object_Cache_Installer {

    private const CONFIG_FILE = 'slv-cache-config.php';
    private const DROPIN_FILE = 'object-cache.php';

    /**
     * 获取配置文件路径。
     */
    public static function get_config_path(): string {
        return WP_CONTENT_DIR . '/' . self::CONFIG_FILE;
    }

    /**
     * 获取 drop-in 路径。
     */
    public static function get_dropin_path(): string {
        return WP_CONTENT_DIR . '/' . self::DROPIN_FILE;
    }

    /**
     * 保存配置并安装 drop-in。
     *
     * @return array{success:bool,message:string}
     */
    public static function install( array $config ): array {
        // 写入配置
        $config_content = "<?php\n// SunLyvo Nexus — Redis Cache Config\n// 此文件由后台自动生成，请勿手动编辑。\nreturn " . var_export( [
            'host'     => $config['host'] ?? '127.0.0.1',
            'port'     => (int) ( $config['port'] ?? 6379 ),
            'password' => $config['password'] ?? '',
            'database' => (int) ( $config['database'] ?? 0 ),
            'prefix'   => $config['prefix'] ?? 'slv:',
            'timeout'  => (float) ( $config['timeout'] ?? 1.0 ),
            'enabled'  => ! empty( $config['enabled'] ),
        ], true ) . ";\n";

        if ( false === file_put_contents( self::get_config_path(), $config_content, LOCK_EX ) ) {
            return [ 'success' => false, 'message' => '配置文件写入失败，请检查 wp-content/ 权限。' ];
        }
        @chmod( self::get_config_path(), 0600 );

        // 测试连接
        $test = self::test_connection( $config );
        if ( ! $test['success'] ) {
            return $test;
        }

        // 写入 drop-in
        if ( false === file_put_contents( self::get_dropin_path(), self::get_dropin_content(), LOCK_EX ) ) {
            return [ 'success' => false, 'message' => 'drop-in 写入失败，请检查 wp-content/ 权限。' ];
        }

        return [ 'success' => true, 'message' => 'Redis 对象缓存已启用。' ];
    }

    /**
     * 卸载 drop-in。
     */
    public static function uninstall(): array {
        $dropin = self::get_dropin_path();
        if ( file_exists( $dropin ) ) {
            // 只删除我们生成的（检查标记）
            $content = (string) file_get_contents( $dropin );
            if ( str_contains( $content, 'SunLyvo Nexus — Object Cache' ) ) {
                @unlink( $dropin );
            } else {
                return [ 'success' => false, 'message' => 'object-cache.php 不是 SunLyvo 生成的，拒绝删除。' ];
            }
        }
        return [ 'success' => true, 'message' => '对象缓存已卸载。' ];
    }

    /**
     * 测试 Redis 连接。
     */
    public static function test_connection( array $config ): array {
        if ( ! class_exists( 'Redis' ) ) {
            return [ 'success' => false, 'message' => 'PHP Redis 扩展未安装。请在 1Panel → PHP → 安装扩展 中启用 redis。' ];
        }

        try {
            $redis = new Redis();
            $ok = @$redis->connect(
                $config['host'] ?? '127.0.0.1',
                (int) ( $config['port'] ?? 6379 ),
                (float) ( $config['timeout'] ?? 1.0 )
            );
            if ( ! $ok ) {
                return [ 'success' => false, 'message' => 'Redis 连接失败：无法连接到 ' . ( $config['host'] ?? '127.0.0.1' ) . ':' . ( $config['port'] ?? 6379 ) ];
            }
            if ( ! empty( $config['password'] ) ) {
                $redis->auth( $config['password'] );
            }
            if ( isset( $config['database'] ) ) {
                $redis->select( (int) $config['database'] );
            }
            $pong = $redis->ping();
            $redis->close();

            if ( 'PONG' !== $pong && true !== $pong ) {
                return [ 'success' => false, 'message' => 'Redis PING 异常：' . var_export( $pong, true ) ];
            }
            return [ 'success' => true, 'message' => 'Redis 连接成功（PING 返回 PONG）。' ];
        } catch ( \Throwable $e ) {
            return [ 'success' => false, 'message' => 'Redis 连接异常：' . $e->getMessage() ];
        }
    }

    /**
     * 检查当前是否已启用。
     */
    public static function is_enabled(): bool {
        return file_exists( self::get_dropin_path() );
    }

    /**
     * 读取当前配置。
     */
    public static function get_config(): array {
        $path = self::get_config_path();
        if ( ! file_exists( $path ) ) {
            return [
                'host'     => '127.0.0.1',
                'port'     => 6379,
                'password' => '',
                'database' => 0,
                'prefix'   => 'slv:',
                'timeout'  => 1.0,
                'enabled'  => false,
            ];
        }
        $config = include $path;
        return is_array( $config ) ? $config : [];
    }

    /**
     * drop-in 内容。
     */
    private static function get_dropin_content(): string {
        return <<<'PHP'
<?php
/**
 * SunLyvo Nexus — Object Cache
 *
 * 由 SunLyvo Nexus 自动生成的 Redis 对象缓存 drop-in。
 * 配置读取自 wp-content/slv-cache-config.php。
 *
 * @package SunLyvo_Nexus
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redis' ) ) {
    return;
}

$slv_config_file = WP_CONTENT_DIR . '/slv-cache-config.php';
if ( ! file_exists( $slv_config_file ) ) {
    return;
}

$slv_cache_config = include $slv_config_file;
if ( ! is_array( $slv_cache_config ) || empty( $slv_cache_config['enabled'] ) ) {
    return;
}

class SLV_Redis_Object_Cache {
    private $redis;
    private $cache = [];
    private $global_groups = [ 'blog-details', 'blog-lookup', 'blog-id-cache', 'networks', 'sites', 'rss' ];
    private $non_persistent_groups = [ 'counts', 'plugins' ];
    private $prefix;

    public function __construct( array $config ) {
        $this->redis = new Redis();
        try {
            $this->redis->connect(
                $config['host'] ?? '127.0.0.1',
                (int) ( $config['port'] ?? 6379 ),
                (float) ( $config['timeout'] ?? 1.0 )
            );
            if ( ! empty( $config['password'] ) ) {
                $this->redis->auth( $config['password'] );
            }
            if ( isset( $config['database'] ) ) {
                $this->redis->select( (int) $config['database'] );
            }
        } catch ( \Throwable $e ) {
            // 连接失败，静默降级到 WP 默认内存缓存
            $this->redis = null;
        }
        $this->prefix = $config['prefix'] ?? 'slv:';
    }

    private function build_key( $key, $group ) {
        return $this->prefix . get_current_blog_id() . ':' . $group . ':' . $key;
    }

    public function get( $key, $group = 'default', $force = false, &$found = null ) {
        if ( empty( $group ) ) $group = 'default';
        $cache_key = $this->build_key( $key, $group );
        if ( ! $force && isset( $this->cache[ $cache_key ] ) ) {
            $found = true;
            return $this->cache[ $cache_key ];
        }
        if ( ! $this->redis ) {
            $found = false;
            return false;
        }
        $value = $this->redis->get( $cache_key );
        if ( false === $value ) {
            $found = false;
            return false;
        }
        $value = @unserialize( $value );
        $this->cache[ $cache_key ] = $value;
        $found = true;
        return $value;
    }

    public function set( $key, $value, $group = 'default', $expire = 0 ) {
        if ( empty( $group ) ) $group = 'default';
        $cache_key = $this->build_key( $key, $group );
        $this->cache[ $cache_key ] = $value;
        if ( ! $this->redis ) {
            return true;
        }
        $serialized = serialize( $value );
        if ( $expire > 0 ) {
            return $this->redis->setex( $cache_key, (int) $expire, $serialized );
        }
        return $this->redis->set( $cache_key, $serialized );
    }

    public function add( $key, $value, $group = 'default', $expire = 0 ) {
        if ( $this->get( $key, $group ) !== false ) {
            return false;
        }
        return $this->set( $key, $value, $group, $expire );
    }

    public function replace( $key, $value, $group = 'default', $expire = 0 ) {
        return $this->set( $key, $value, $group, $expire );
    }

    public function delete( $key, $group = 'default' ) {
        $cache_key = $this->build_key( $key, $group );
        unset( $this->cache[ $cache_key ] );
        if ( ! $this->redis ) return false;
        return $this->redis->del( $cache_key );
    }

    public function incr( $key, $offset = 1, $group = 'default' ) {
        if ( ! $this->redis ) return false;
        return $this->redis->incrBy( $this->build_key( $key, $group ), (int) $offset );
    }

    public function decr( $key, $offset = 1, $group = 'default' ) {
        if ( ! $this->redis ) return false;
        return $this->redis->decrBy( $this->build_key( $key, $group ), (int) $offset );
    }

    public function flush() {
        $this->cache = [];
        if ( ! $this->redis ) return false;
        return $this->redis->flushDB();
    }

    public function add_global_groups( $groups ) {
        foreach ( (array) $groups as $g ) {
            $this->global_groups[] = $g;
        }
    }

    public function add_non_persistent_groups( $groups ) {
        foreach ( (array) $groups as $g ) {
            $this->non_persistent_groups[] = $g;
        }
    }

    public function switch_to_blog( $blog_id ) {
        $this->cache = [];
    }

    public function reset() {
        $this->cache = [];
    }
}

if ( empty( $GLOBALS['wp_object_cache'] ) || ! $GLOBALS['wp_object_cache'] instanceof SLV_Redis_Object_Cache ) {
    $GLOBALS['wp_object_cache'] = new SLV_Redis_Object_Cache( $slv_cache_config );
}
PHP;
    }
}