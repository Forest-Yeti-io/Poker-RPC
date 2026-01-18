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
    "7-Clubs",
    "6-Hearts",
    "13-Spades"
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
    "Cards": ["14-Spades", "13-Spades", "12-Spades", "11-Spades", "10-Spades"]
  },
  "Token": "<APPLICATION_SECRET_TOKEN>"
}
```

**Пример ответа:**
```json
{
  "score": 9,
  "combinationName": "Royal Flash",
  "playingCards": ["14-Spades", "13-Spades", "12-Spades", "11-Spades", "10-Spades"]
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
  "Token": "Test",
  "Params": {
    "BoardCards": [
      "13-Clubs",
      "2-Hearts",
      "12-Hearts",
      "8-Hearts"
    ],
    "Players": {
      "P1": [
        "14-Hearts",
        "6-Clubs"
      ],
      "P2": [
        "8-Diamonds",
        "8-Clubs"
      ],
      "P3": [
        "13-Diamonds",
        "13-Hearts"
      ]
    }
  }
}
```

**Пример ответа:**
```json
{
  "resolvers": [
    {
      "playerIdentifier": "P1",
      "score": 1,
      "combinationName": "High Card",
      "playingCards": [
        "14-Hearts",
        "13-Clubs",
        "12-Hearts",
        "8-Hearts",
        "6-Clubs"
      ],
      "equity": 19.047619047619047
    },
    {
      "playerIdentifier": "P2",
      "score": 300,
      "combinationName": "Three Of Kind",
      "playingCards": [
        "8-Diamonds",
        "8-Clubs",
        "8-Hearts",
        "13-Clubs",
        "12-Hearts"
      ],
      "equity": 2.380952380952381
    },
    {
      "playerIdentifier": "P3",
      "score": 300,
      "combinationName": "Three Of Kind",
      "playingCards": [
        "13-Diamonds",
        "13-Hearts",
        "13-Clubs",
        "12-Hearts",
        "8-Hearts"
      ],
      "equity": 78.57142857142857
    }
  ],
  "winners": [
    "P3"
  ]
}
```

## Формат карт

Карты передаются строками в формате `<Rank>-<Suit>` (например, `14-Spades`, `10-Hearts`). Доступные значения рангов и мастей должны соответствовать перечислениям `CardRankEnum` и `CardSuitEnum` библиотеки PokerKernel.
