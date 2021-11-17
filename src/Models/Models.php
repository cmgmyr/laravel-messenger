<?php

namespace Cmgmyr\Messenger\Models;

use Illuminate\Database\Eloquent\Model;

class Models
{
    /**
     * Map for the messenger's models.
     *
     * @var array
     */
    protected static $models = [];

    /**
     * Map for the messenger's tables.
     *
     * @var array
     */
    protected static $tables = [];

    /**
     * Internal pointer name for the app's "user" model.
     *
     * @var string
     */
    private static $userModelLookupKey = 'User';

    /**
     * Set the model to be used for threads.
     *
     * @param string $model
     */
    public static function setMessageModel(string $model): void
    {
        static::$models[Message::class] = $model;
    }

    /**
     * Set the model to be used for participants.
     *
     * @param  string $model
     */
    public static function setParticipantModel(string $model): void
    {
        static::$models[Participant::class] = $model;
    }

    /**
     * Set the model to be used for threads.
     *
     * @param  string $model
     */
    public static function setThreadModel(string $model): void
    {
        static::$models[Thread::class] = $model;
    }

    /**
     * Set the model to be used for users.
     *
     * @param  string  $model
     */
    public static function setUserModel(string $model): void
    {
        static::$models[self::$userModelLookupKey] = $model;
    }

    /**
     * Set custom table names.
     *
     * @param  array $map
     */
    public static function setTables(array $map): void
    {
        static::$tables = array_merge(static::$tables, $map);
    }

    /**
     * Get a custom table name mapping for the given table.
     *
     * @param  string $table
     * @return string
     */
    public static function table(string $table): string
    {
        return static::$tables[$table] ?? $table;
    }

    /**
     * Get the class name mapping for the given model.
     *
     * @param  string $model
     * @return string
     */
    public static function classname(string $model): string
    {
        return static::$models[$model] ?? $model;
    }

    /**
     * Get an instance of the messages model.
     *
     * @param  array $attributes
     * @return \Cmgmyr\Messenger\Models\Message
     */
    public static function message(array $attributes = []): Message
    {
        return static::make(Message::class, $attributes);
    }

    /**
     * Get an instance of the participants model.
     *
     * @param  array $attributes
     * @return \Cmgmyr\Messenger\Models\Participant
     */
    public static function participant(array $attributes = []): Participant
    {
        return static::make(Participant::class, $attributes);
    }

    /**
     * Get an instance of the threads model.
     *
     * @param  array $attributes
     * @return \Cmgmyr\Messenger\Models\Thread
     */
    public static function thread(array $attributes = []): Thread
    {
        return static::make(Thread::class, $attributes);
    }

    /**
     * Get an instance of the user model.
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function user(array $attributes = []): Model
    {
        return static::make(self::$userModelLookupKey, $attributes);
    }

    /**
     * Get an instance of the given model.
     *
     * @param  string $model
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected static function make(string $model, array $attributes = []): Model
    {
        $model = static::classname($model);

        return new $model($attributes);
    }
}
