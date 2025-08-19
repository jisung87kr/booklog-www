<?php

namespace App\Console\Commands;

use App\Models\Persona;
use App\Services\PersonaFeedService;
use Illuminate\Console\Command;

class GenerateScheduledFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feeds:generate-scheduled {--dry-run : Show what would be generated without actually generating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate feeds for personas that are scheduled to publish now';

    protected PersonaFeedService $feedService;

    public function __construct(PersonaFeedService $feedService)
    {
        parent::__construct();
        $this->feedService = $feedService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 스케줄된 AI 피드 생성을 시작합니다...');
        
        // 발행할 준비가 된 페르소나들 조회
        $readyPersonas = Persona::readyToPublish()->get();
        
        if ($readyPersonas->isEmpty()) {
            $this->info('✅ 현재 발행할 페르소나가 없습니다.');
            return Command::SUCCESS;
        }

        $this->info("📋 발행 대상 페르소나: {$readyPersonas->count()}개");
        
        if ($this->option('dry-run')) {
            $this->info('🔍 드라이런 모드: 실제 생성하지 않고 대상만 표시합니다.');
            
            foreach ($readyPersonas as $persona) {
                $this->line("  - {$persona->name} ({$persona->schedule_description})");
            }
            
            return Command::SUCCESS;
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($readyPersonas as $persona) {
            try {
                $this->info("🎯 {$persona->name} 피드 생성 중...");
                
                // 페르소나에 할당된 사용자 중 하나를 랜덤 선택
                $user = $persona->users()->inRandomOrder()->first();
                
                if (!$user) {
                    $this->warn("⚠️  {$persona->name}: 할당된 사용자가 없습니다.");
                    continue;
                }

                // 피드 생성
                $post = $this->feedService->generateFeedForPersona($user);
                
                // 발행 완료 처리
                $persona->markAsPublished();
                
                $this->info("✅ {$persona->name}: 피드 생성 완료 (ID: {$post->id})");
                $successCount++;
                
            } catch (\Exception $e) {
                $errorMessage = "{$persona->name}: " . $e->getMessage();
                $errors[] = $errorMessage;
                $errorCount++;
                
                $this->error("❌ {$errorMessage}");
                
                // 로그에 상세 에러 기록
                logger()->error("스케줄된 피드 생성 실패", [
                    'persona_id' => $persona->id,
                    'persona_name' => $persona->name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // 결과 요약
        $this->newLine();
        $this->info("📊 피드 생성 완료:");
        $this->info("  ✅ 성공: {$successCount}개");
        
        if ($errorCount > 0) {
            $this->error("  ❌ 실패: {$errorCount}개");
            $this->newLine();
            $this->error("❌ 실패 목록:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }

        return $errorCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
