# quanma51
Quan Ma 51 Api SDK 

# Usage

```
$username     = env('QUANMA_USER_NAME');
$password     = env('QUANMA_PASSWORD');
$key          = env('QUANMA_KEY');
$code         = env('QUANMA_CODE');
$quanma       = new Client($username, $password, $key, $code);

$quanma->coupon($id);

$quanma->coupons();

$quanma->buy($id);
```
