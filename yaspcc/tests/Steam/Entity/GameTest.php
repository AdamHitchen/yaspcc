<?php declare(strict_types=1);

namespace Yaspcc\Tests\Steam\Entity;

use PHPUnit\Framework\TestCase;
use Yaspcc\Steam\Entity\Game;

class GameTest extends TestCase
{
    /** @test */
    public function can_create_game_from_json_object_with_valid_data()
    {
        $gameObj = new \stdClass();
        $gameObj->isLinuxNative = true;
        $gameObj->hasCompleteData = false;
        $gameObj->imageUrl = 'http://somesite.example/img.jpg';

        $game = new Game("test game", 200);
        $game->fromJson($gameObj);

        $this->assertEquals($gameObj->isLinuxNative, $game->isLinuxNative);
        $this->assertEquals($gameObj->hasCompleteData, $game->hasCompleteData);
        $this->assertEquals($gameObj->imageUrl, $game->imageUrl);
    }
}