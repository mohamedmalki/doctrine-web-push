<?php

namespace DoctrineWeb\WebPush;



use Illuminate\Database\Eloquent\Model;

/**
 * @property string $endpoint
 * @property string|null $public_key
 * @property string|null $auth_token
 * @property string|null $content_encoding
 */
class PushSubscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
    ];

    /**
     * Create a new model instance.
     *
     * @param  array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('doctrinewebpush.database_connection'));
        }

        if (! isset($this->table)) {
            $this->setTable(config('doctrinewebpush.table_name'));
        }

        parent::__construct($attributes);
    }

    /**
     * Find a subscription by the given endpint.
     *
     * @param  string $endpoint
     * @return static|null
     */
    public static function findByEndpoint($endpoint)
    {
        return static::where('endpoint', $endpoint)->first();
    }
}