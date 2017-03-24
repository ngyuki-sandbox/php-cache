# Example PSR-6: PSR-Cache

PSR-6 の例

- http://www.php-fig.org/psr/psr-6/

```php
$pool = new CachePool(__DIR__ . '/../data/');

// notfound なアイテムが返る
$item = $pool->getItem('xxx');

// 値を設定（有効期限はデフォルト値）
$item->set(123);

// 有効期限を設定
$item->expiresAfter(300);

// 変更をバックエンドのストレージに反映
$this->commit();

// コミットせずに直ちに指定のアイテムだけストレージに反映
$this->save($item);

// ？？？
// デフォルトで即時反映される実装であえて遅延させたいとき？
$this->saveDeferred($item);
```

プールに対して set/add するのではないところがちょっとおもしろいかも？
実際に使うときは↓のようになるか。

```php
$item = $pool->getItem('xxx');

if ($item->isHit() === false) {
    $row = $dao->fetchRow();
    $item->set($row);
}

return $item->get();
```
