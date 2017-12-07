<?php
namespace App;

class TokenRepository
{
    public function createActivationTokens(User $user)
    {
        $this->purgeTokensForUser($user);

        return [
            'activate' => $this->createToken($user, 'activate'),
            'cancel' => $this->createToken($user, 'cancel'),
        ];
    }

    private function createToken(User $user, $tokenType)
    {
        $token = hash_hmac('sha256', str_random(40), config('app.key'));

        UserToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'token_type' => $tokenType,
        ]);

        return $token;
    }

    public function get($token, $tokenType)
    {
        return UserToken::where('token', '=', $token)
            ->where('token_type', '=', $tokenType)
            ->first();
    }

    public function getByUser(User $user, $tokenType)
    {
        return UserToken::where('user_id', '=', $user->id)
            ->where('token_type', '=', $tokenType)
            ->first();
    }

    public function purgeTokensForUser(User $user)
    {
        return UserToken::where('user_id', '=', $user->id)
            ->delete();
    }
}
