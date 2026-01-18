# Poker-RPC

Документация описывает RPC-вызовы сервиса Poker-RPC и формат запросов к единому HTTP-эндпоинту.

## Базовая информация

- **HTTP-эндпоинт:** `POST /main`
- **Формат запроса/ответа:** JSON
- **Авторизация:** статический токен в поле `Token`
- **Переменная окружения:** `APPLICATION_SECRET_TOKEN` (используется для проверки токена)

## Общий формат запроса

```json
{
  "Method": "<ИмяМетода>",
  "Params": { "...": "..." },
  "Token": "<APPLICATION_SECRET_TOKEN>"
}
```

Где:
- `Method` — имя RPC-метода (см. список ниже).
- `Params` — объект с параметрами метода.
- `Token` — секретный токен приложения.

## Общий формат ответа

Успешный ответ всегда приходит с HTTP 200 и JSON-объектом, определённым конкретным методом. При ошибке валидации или авторизации возвращается HTTP 400 с сообщением:

```json
{
  "message": "Текст ошибки"
}
```

## Методы RPC

### 1. `Ping`
Проверка доступности сервиса.

**Параметры:**
- `WithSayHello` (bool, необязательный) — если `true`, сервис отвечает приветствием.

**Пример запроса:**
```json
{
  "Method": "Ping",
  "Params": {
    "WithSayHello": true
  },
  "Token": "<APPLICATION_SECRET_TOKEN>"
}
```

**Пример ответа:**
```json
{
  "message": "Hello, World!"
}
```

---

### 2. `GetHoldemRandomCardDeck`
Возвращает случайно перемешанную колоду для Texas Hold'em.

**Параметры:** отсутствуют.

**Пример запроса:**
```json
{
  "Method": "GetHoldemRandomCardDeck",
  "Params": {},
  "Token": "<APPLICATION_SECRET_TOKEN>"
}
```

**Пример ответа:**
```json
{
  "cardDeck": [
    "A-S",
    "K-H",
    "10-D"
  ]
}
```

---

### 3. `GetHoldemCombination`
Возвращает комбинацию, собранную из переданных карт (карты игрока + общие карты).

**Параметры:**
- `Cards` (array of string, обязательный) — список карт в формате `<Rank>-<Suit>`.

**Пример запроса:**
```json
{
  "Method": "GetHoldemCombination",
  "Params": {
    "Cards": ["A-S", "K-S", "Q-S", "J-S", "10-S"]
  },
  "Token": "<APPLICATION_SECRET_TOKEN>"
}
```

**Пример ответа:**
```json
{
  "score": 9,
  "combinationName": "Royal Flash",
  "playingCards": ["A-S", "K-S", "Q-S", "J-S", "10-S"]
}
```

---

### 4. `GetHoldemWinner`
Определяет победителей и эквити для каждого игрока по заданным картам на столе и рукам игроков.

**Параметры:**
- `BoardCards` (array of string, обязательный) — карты на столе в формате `<Rank>-<Suit>`.
- `Players` (object, обязательный) — объект, где ключ — идентификатор игрока, значение — массив из двух карт игрока.

**Пример запроса:**
```json
{
  "Method": "GetHoldemWinner",
  "Params": {
    "BoardCards": ["A-S", "K-S", "Q-S", "J-S", "2-D"],
    "Players": {
      "P1": ["10-S", "9-S"],
      "P2": ["A-D", "A-H"]
    }
  },
  "Token": "<APPLICATION_SECRET_TOKEN>"
}
```

**Пример ответа:**
```json
{
  "resolvers": [
    {
      "playerIdentifier": "P1",
      "score": 9,
      "combinationName": "Royal Flash",
      "playingCards": ["A-S", "K-S", "Q-S", "J-S", "10-S"],
      "equity": 0.75
    },
    {
      "playerIdentifier": "P2",
      "score": 1,
      "combinationName": "Pair",
      "playingCards": ["A-D", "A-H", "A-S", "K-S", "Q-S"],
      "equity": 0.25
    }
  ],
  "winners": ["P1"]
}
```

## Формат карт

Карты передаются строками в формате `<Rank>-<Suit>` (например, `A-S`, `10-H`). Доступные значения рангов и мастей должны соответствовать перечислениям `CardRankEnum` и `CardSuitEnum` библиотеки PokerKernel.
