<?php declare(strict_types=1);

namespace Yaspcc\Steam\Repository;

use Yaspcc\Cache\KeyValueCacheInterface;
use Yaspcc\Steam\Entity\User\Profile;
use Yaspcc\Steam\Exception\UserNotFoundException;
use Yaspcc\Steam\Request\ProfileRequest;

class ProfileRepository
{
    /**
     * @var KeyValueCacheInterface
     */
    private $cache;
    /**
     * @var ProfileRequest
     */
    private $profileRequest;

    /**
     * GameRepository constructor.
     * @param KeyValueCacheInterface $cache
     * @param ProfileRequest $profileRequest
     */
    public function __construct(KeyValueCacheInterface $cache, ProfileRequest $profileRequest)
    {
        $this->cache = $cache;
        $this->profileRequest = $profileRequest;
    }

    /**
     * @param string $id
     * @return Profile
     * @throws UserNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $id): Profile
    {
        if ($this->cache->exists("profile:" . $id)) {
            $json = $this->cache->get("profile:" . $id);
        } else {

            $profile = $this->profileRequest->getUserProfile($id);
            $this->set($profile);
        }

        if (!empty($profile)) {
            return $profile;
        } elseif (!empty($json)) {
            return $this->createProfileFromJson($json);

        } else {
            throw new UserNotFoundException();
        }

        return $this->createProfileFromJson($json);
    }

    /**
     * @param string $json
     * @return Profile
     */
    private function createProfileFromJson(string $json): Profile
    {
        $profileObj = json_decode($json);
        $profile = new Profile($profileObj->userId);
        return $profile->fromJson($profileObj);
    }

    /**
     * @param Profile $profile
     */
    public function set(Profile $profile)
    {
        $this->cache->set("profile:" . $profile->userId, json_encode($profile));

        if (!empty($profile->username)) {
            $this->cache->set("profile:" . $profile->username, json_encode($profile));
        }
    }
}