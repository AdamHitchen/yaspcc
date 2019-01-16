<?php declare(strict_types=1);

namespace Yaspcc\Tests\Cron;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Yaspcc\Cache\KeyValueCacheInterface;
use Yaspcc\Cron\Queue;
use Yaspcc\Steam\Entity\Game;
use Yaspcc\Steam\Exception\ApiLimitExceededException;
use Yaspcc\Steam\Exception\GameNotFoundException;
use Yaspcc\Steam\Repository\GameRepository;

class QueueTest extends \PHPUnit\Framework\TestCase
{

    private $gameRepository;
    private $cache;
    private $logger;

    public function setUp()
    {
        parent::setUp();
        $this->gameRepository = Mockery::mock(GameRepository::class);
        $this->cache = Mockery::mock(KeyValueCacheInterface::class);
        $this->logger = Mockery::mock(LoggerInterface::class);
    }

    private function getExampleGames() : array
    {
        return [
            new Game("Counter-Strike: Global Offensive", 730),
            new Game("Alien Swarm", 630)
        ];
    }

    /** @test */
    public function can_process_queue_without_cache()
    {
        //Valid example of games from the steam store
        $this->gameRepository->shouldReceive('getAllApps')->andReturn([
            "applist" => [
                "apps" => json_decode('[{"appid":730,"name":"Counter-Strike: Global Offensive"},{"appid":630,"name":"Alien Swarm"}]',
                    true)
            ]
        ]);

        $games = $this->getExampleGames();
        $this->gameRepository->shouldReceive('get')->with($games[0]->id)->andReturn($games[0]);
        $this->gameRepository->shouldReceive('get')->with($games[1]->id)->andReturn($games[1]);

        $this->cache->shouldReceive('exists')->andReturnFalse();
        $this->cache->shouldReceive("set")->with("queue",'[]');

        $this->logger->shouldNotHaveBeenCalled();

        $queue = new Queue($this->gameRepository, $this->cache, $this->logger);
        //Nothing should be returns & no exceptions should be thrown
        $this->assertNull($queue->processQueue());
    }

    /** @test */
    public function can_process_queue_with_queue_cache()
    {
        //Valid example of games from the steam store

        $games = $this->getExampleGames();
        $this->gameRepository->shouldReceive('get')->with($games[0]->id)->andReturn($games[0]);
        $this->gameRepository->shouldReceive('get')->with($games[1]->id)->andReturn($games[1]);
        $this->cache->shouldReceive('exists')->with('queue')->andReturn(true);
        $this->cache->shouldReceive('get')
            ->with('queue')
            ->andReturn('[{"appid":730,"name":"Counter-Strike: Global Offensive"},{"appid":630,"name":"Alien Swarm"}]');
        $this->cache->shouldReceive("set")->with("queue",'[]');
        $this->cache->shouldReceive("exists")->with("game:".$games[0]->id)->andReturnFalse();
        $this->cache->shouldReceive("exists")->with("game:".$games[1]->id)->andReturnFalse();

        $this->logger->shouldNotHaveBeenCalled();

        $queue = new Queue($this->gameRepository, $this->cache, $this->logger);

        $this->assertNull($queue->processQueue());

        $this->gameRepository->shouldHaveReceived('get');

    }

    /** @test */
    public function can_process_queue_with_queue_cache_and_game_cache()
    {
        //Valid example of games from the steam store

        $game = new Game("Counter-Strike: Global Offensive", 730);
        $game2 = new Game("Alien Swarm", 630);
        $this->cache->shouldReceive('exists')->with('queue')->andReturn(true);
        $this->cache->shouldReceive('get')
            ->with('queue')
            ->andReturn('[{"appid":730,"name":"Counter-Strike: Global Offensive"},{"appid":630,"name":"Alien Swarm"}]');
        $this->cache->shouldReceive("set")->with("queue",'[]');
        $this->cache->shouldReceive("exists")->with("game:".$game->id)->andReturnTrue();
        $this->cache->shouldReceive("exists")->with("game:".$game2->id)->andReturnTrue();

        $this->logger->shouldNotHaveBeenCalled();

        $queue = new Queue($this->gameRepository, $this->cache, $this->logger);



        $this->assertNull($queue->processQueue());

        $this->gameRepository->shouldNotHaveBeenCalled();
        $this->gameRepository->shouldNotHaveReceived('get');

    }

    /** @test */
    public function throws_exception_when_api_limit_reached(){
        //Valid example of games from the steam store

        $games = $this->getExampleGames();
        $this->gameRepository->shouldReceive('get')->with($games[0]->id)->andReturn($games[0]);
        $this->gameRepository->shouldReceive('get')->with($games[1]->id)->andThrow(ApiLimitExceededException::class);
        $this->cache = Mockery::mock(KeyValueCacheInterface::class);
        $this->cache->shouldReceive('exists')->with('queue')->andReturn(true);
        $this->cache->shouldReceive('get')
            ->with('queue')
            ->andReturn('[{"appid":730,"name":"Counter-Strike: Global Offensive"},{"appid":630,"name":"Alien Swarm"}]');
        $this->cache->shouldReceive("set")->with("queue",'[]');
        $this->cache->shouldReceive("exists")->with("game:".$games[0]->id)->andReturnFalse();
        $this->cache->shouldReceive("exists")->with("game:".$games[1]->id)->andReturnFalse();

        $this->logger->shouldReceive('alert');

        $queue = new Queue($this->gameRepository, $this->cache, $this->logger);

        $this->assertNull($queue->processQueue());

        $this->gameRepository->shouldHaveReceived('get');
    }

    /** @test */
    public function throws_exception_when_game_not_found()
    {
        $this->gameRepository = Mockery::mock(GameRepository::class);
        //Valid example of games from the steam store

        $games = $this->getExampleGames();
        $this->gameRepository->shouldReceive('get')->with($games[0]->id)->andReturn($games[0]);
        $this->gameRepository->shouldReceive('get')->with($games[1]->id)->andThrow(GameNotFoundException::class);
        $this->cache = Mockery::mock(KeyValueCacheInterface::class);
        $this->cache->shouldReceive('exists')->with('queue')->andReturn(true);
        $this->cache->shouldReceive('get')
            ->with('queue')
            ->andReturn('[{"appid":730,"name":"Counter-Strike: Global Offensive"},{"appid":630,"name":"Alien Swarm"}]');
        $this->cache->shouldReceive("set")->with("queue",'[]');
        $this->cache->shouldReceive("exists")->with("game:".$games[0]->id)->andReturnFalse();
        $this->cache->shouldReceive("exists")->with("game:".$games[1]->id)->andReturnFalse();

        $this->logger->shouldReceive('alert');

        $queue = new Queue($this->gameRepository, $this->cache, $this->logger);

        $this->assertNull($queue->processQueue());

        $this->gameRepository->shouldHaveReceived('get');

    }

}