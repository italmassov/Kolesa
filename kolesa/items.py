# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

import scrapy

class KolesaItem(scrapy.Item):
    crawlDateTime = scrapy.Field()  # Время сливания данных

    region = scrapy.Field()         # регион владельца
    bodyType = scrapy.Field()       # легковые грузовые
    carMarket = scrapy.Field()      # primary market, secondary market
    carMake = scrapy.Field()        # производитель машины, например Toyota
    carModel = scrapy.Field()       # модель машины, например Camry
    carYear = scrapy.Field()        # год выпуска модели
    engineVol = scrapy.Field()      # объем двигателя
    engineType = scrapy.Field()     # тип двигателя
    transmission = scrapy.Field()   # коробка передач
    condition = scrapy.Field()      # состояние
    mileage = scrapy.Field()        # пробег
    steeringWheel = scrapy.Field()  # расположение руля
    gearType = scrapy.Field()       # привод: передний, задний, 4D
    color = scrapy.Field()          # цвет
    customsState = scrapy.Field()   # растаможен в Казахстане: да, нет

    configDiscs = scrapy.Field()        # диски
    configDodger = scrapy.Field()       # обвес
    configTinting = scrapy.Field()      # тонировка
    configWinch = scrapy.Field()        # лебедка
    configHatch = scrapy.Field()        # люк
    configWindshields = scrapy.Field()  # ветровики
    configBullbar = scrapy.Field()      # кенгурятник
    configRailing = scrapy.Field()      # рейлинги
    configSpoiler = scrapy.Field()      # спойлер
    configTrunk = scrapy.Field()        # багажник

    configXenon = scrapy.Field()            # ксенон
    configHeadlightWasher = scrapy.Field()  # омыватель фар
    configBiXenon = scrapy.Field()          # биксенон
    configHeadlightCor = scrapy.Field()     # коректор фар
    configHeadlightCryst = scrapy.Field()   # хрустальная оптика
    configMirriorWarm = scrapy.Field()      # обогреватель зеркал
    configAntiFog = scrapy.Field()          # противотуманки

    configVelour = scrapy.Field()           # велюр
    configAlcantara = scrapy.Field()        # алькантара
    configLeather = scrapy.Field()          # кожа
    configCombi = scrapy.Field()            # комбинированный
    configWood = scrapy.Field()             # дерево
    configShields = scrapy.Field()          # шторки

    configQuadro = scrapy.Field()           # квадросистема
    configUSB = scrapy.Field()              # USB
    configCD = scrapy.Field()               # CD
    configDVD = scrapy.Field()              # DVD
    configCDChanger = scrapy.Field()        # CD changer
    configDVDChanger = scrapy.Field()       # DVD changer
    configMP3 = scrapy.Field()              # MP3
    configSubwoofer = scrapy.Field()        # сабвуфер

    configSteerHydr = scrapy.Field()        # ГУР
    configBoardPC = scrapy.Field()          # бортовой компьютер
    configABS = scrapy.Field()              # ABS
    configNavigation = scrapy.Field()       # навигационная система
    configSRS = scrapy.Field()              # SRS
    configBluetooth = scrapy.Field()        # Bluetooth
    configWinterMode = scrapy.Field()       # зимний режим
    configMultiSteer = scrapy.Field()       # мультируль
    configSportMode = scrapy.Field()        # спортивный режим
    configSteerWarm = scrapy.Field()        # подогрев руля
    configTurboPressure = scrapy.Field()    # турбо наддув
    configSeatWarm = scrapy.Field()         # подогрев сидений
    configTurboTimer = scrapy.Field()       # турботаймер
    configSeatMemory = scrapy.Field()       # память сидений
    configAlarm = scrapy.Field()            # сигнализация
    configSteerMemory = scrapy.Field()      # память руля
    configAutoIgnition = scrapy.Field()     # автозавод
    configParktronics = scrapy.Field()      # парктроники
    configImmobilizer = scrapy.Field()      # имобилайзер
    configRearCam = scrapy.Field()          # камера заднего вида
    configElectr = scrapy.Field()           # электропакет
    configLightSens = scrapy.Field()        # датчик света
    configCentralLocker = scrapy.Field()    # центральный замок
    configRainSensor = scrapy.Field()       # датчик дождя
    configAC = scrapy.Field()               # кондиционер
    configTirePressure = scrapy.Field()     # датчик давления в шинах
    configClimate = scrapy.Field()          # климат контроль
    configAirSuspension = scrapy.Field()    # пневмоподвеска
    configCruise = scrapy.Field()           # круиз контроль
    configClearance = scrapy.Field()        # изменяемый клиренс

    configFreshDriven = scrapy.Field()      # свежепригнан
    configFreshDelivered = scrapy.Field()   # свежедоставлен
    configTax = scrapy.Field()              # налог уплачен
    configMaintenance = scrapy.Field()      # техосмотр
    configNoInvest = scrapy.Field()         # вложения

    AdViews = scrapy.Field()                # просмотров объявления
    PostDate = scrapy.Field()               # дата размещения объявления
    SellingPrice = scrapy.Field()           # цена продажи
    AdLink = scrapy.Field()                 # ссылка объявления
    AdPhone = scrapy.Field()                # телефон