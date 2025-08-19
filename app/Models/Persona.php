<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'age',
        'occupation',
        'reading_preferences',
        'description',
        'speaking_style',
        'is_active',
        'auto_publish_enabled',
        'publish_frequency',
        'publish_schedule',
        'last_published_at',
        'next_publish_at'
    ];

    protected $casts = [
        'reading_preferences' => 'array',
        'publish_schedule' => 'array',
        'is_active' => 'boolean',
        'auto_publish_enabled' => 'boolean',
        'last_published_at' => 'datetime',
        'next_publish_at' => 'datetime'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function calculateNextPublishTime()
    {
        if (!$this->auto_publish_enabled || !$this->publish_frequency) {
            return null;
        }

        $now = now();
        $schedule = $this->publish_schedule ?? [];

        switch ($this->publish_frequency) {
            case 'hourly':
                return $now->addHour();
                
            case 'daily':
                $hour = $schedule['hour'] ?? 9;
                $minute = $schedule['minute'] ?? 0;
                
                $nextTime = $now->copy()->setTime($hour, $minute, 0);
                if ($nextTime->lte($now)) {
                    $nextTime->addDay();
                }
                return $nextTime;
                
            case 'weekly':
                $dayOfWeek = $schedule['day_of_week'] ?? 1; // 1 = Monday
                $hour = $schedule['hour'] ?? 9;
                $minute = $schedule['minute'] ?? 0;
                
                $nextTime = $now->copy()
                    ->startOfWeek()
                    ->addDays($dayOfWeek - 1)
                    ->setTime($hour, $minute, 0);
                    
                if ($nextTime->lte($now)) {
                    $nextTime->addWeek();
                }
                return $nextTime;
                
            default:
                return null;
        }
    }

    public function updateNextPublishTime()
    {
        $this->update([
            'next_publish_at' => $this->calculateNextPublishTime()
        ]);
    }

    public function shouldPublishNow()
    {
        return $this->auto_publish_enabled 
            && $this->next_publish_at 
            && $this->next_publish_at->lte(now());
    }

    public function markAsPublished()
    {
        $this->update([
            'last_published_at' => now(),
            'next_publish_at' => $this->calculateNextPublishTime()
        ]);
    }

    public function getPublishFrequencyLabelAttribute()
    {
        return match($this->publish_frequency) {
            'hourly' => '매시간',
            'daily' => '매일',
            'weekly' => '매주',
            default => '설정 없음'
        };
    }

    public function getScheduleDescriptionAttribute()
    {
        if (!$this->auto_publish_enabled || !$this->publish_schedule) {
            return '자동 발행 비활성화';
        }

        $schedule = $this->publish_schedule;
        
        switch ($this->publish_frequency) {
            case 'hourly':
                return '매시간 자동 발행';
                
            case 'daily':
                $hour = $schedule['hour'] ?? 9;
                $minute = $schedule['minute'] ?? 0;
                return sprintf('매일 %02d:%02d에 자동 발행', $hour, $minute);
                
            case 'weekly':
                $dayNames = ['일', '월', '화', '수', '목', '금', '토'];
                $dayOfWeek = $schedule['day_of_week'] ?? 1;
                $hour = $schedule['hour'] ?? 9;
                $minute = $schedule['minute'] ?? 0;
                $dayName = $dayNames[$dayOfWeek] ?? '월';
                return sprintf('매주 %s요일 %02d:%02d에 자동 발행', $dayName, $hour, $minute);
                
            default:
                return '스케줄 설정 오류';
        }
    }

    public function scopeReadyToPublish($query)
    {
        return $query->where('auto_publish_enabled', true)
                    ->where('is_active', true)
                    ->whereNotNull('next_publish_at')
                    ->where('next_publish_at', '<=', now());
    }
}
