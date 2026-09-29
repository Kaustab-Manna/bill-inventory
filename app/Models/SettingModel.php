<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['group', 'key', 'value', 'type'];
    protected $useTimestamps    = true;

    /**
     * Get a setting value by group and key
     */
    public function getSetting(string $group, string $key, $default = null)
    {
        $row = $this->where('group', $group)->where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    /**
     * Set a setting value
     */
    public function setSetting(string $group, string $key, $value, string $type = 'string')
    {
        $existing = $this->where('group', $group)->where('key', $key)->first();
        if ($existing) {
            return $this->update($existing->id, ['value' => $value]);
        }
        return $this->insert(['group' => $group, 'key' => $key, 'value' => $value, 'type' => $type]);
    }

    /**
     * Get all settings for a group
     */
    public function getGroup(string $group): array
    {
        $rows = $this->where('group', $group)->findAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row->key] = $row->value;
        }
        return $settings;
    }
}
