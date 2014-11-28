# -*- coding: utf-8 -*-
__author__ = 'kuanysh'

import sys
import scrapy
from scrapy.selector import HtmlXPathSelector
from scrapy.http import Request
from kolesa.items import KolesaItem
import urlparse
import re
from scrapy.shell import inspect_response
from scrapy import log
from scrapy.utils.response import open_in_browser
from urllib import urlencode
import urllib2
import time
import json
from scrapy.http import FormRequest, Request


class KolesaSpider(scrapy.Spider):
    name = "kolesa"
    #download_delay = 0.1
    allowed_domains = ["kolesa.kz"]
    #"http://kolesa.kz/cars/toyota/camry/region-almatinskaya-oblast/"
    start_urls = [
            "http://kolesa.kz/"
            ]

    # select region and make of the car
    def parse(self, response):
        # extracting list of regions
        #req = urllib2.Request("http://kolesa.kz/ajax/load-regions")
        #resp = urllib2.urlopen(req)
        #print(resp)
        #if resp.getcode != 404:
        #    regList = json.loads(resp)

        # testing specific pages descripton
        #item =  KolesaItem()
        #yield Request("http://kolesa.kz/a/show/12773955", callback = self.parseDescription, meta={'item':item})


        regList = response.xpath('//ul[@class="region-popular-list"]/li[@class="region-item"]')
        for region in regList:
            item = KolesaItem()
            cur_region = ''.join(region.xpath('span[@class="inactive region-city-item"]/text()').extract())
            cur_code = ''.join(region.xpath('span[@class="inactive region-city-item"][@data-region]').extract())
            cur_code_op = cur_code.find('data-region="')
            cur_code_cp = cur_code.find('"', cur_code_op+len('data-region="'))
            cur_code = cur_code[cur_code_op+len('data-region="'):cur_code_cp]

        yield FormRequest.from_response(response, formname="region-form",
            formdata={'backUrl':'/','select_region_list':cur_code, 'select_region_region':cur_region},
            callback=self.parse2)

    def parse2(self, response):
        #open_in_browser(response)
        # поддержанные авто
        yield Request("http://kolesa.kz/cars/", callback = self.parse3)

    def parse3(self, response):
        # go through all makes
        makes = response.xpath('//table[@class="all"]/descendant::a[contains(@href, "http://kolesa.kz/cars/")]/@href').extract()

        #for make in makes:
        for make in makes:
            make_link = ''.join(make)
            yield Request(make_link , callback = self.parse4)

    def parse4(self, response):
        # go through models of the make
        #open_in_browser(response)
        # starting crawling
        Ads = response.xpath('//div[@class="header-search"]')
        for Ad in Ads:
            link = ''.join(Ad.xpath('a[@class="fn"]/@href').extract())

            item =  KolesaItem()
            item['crawlDateTime'] = time.time()
            item['carMarket'] = "Secondary"
            item['AdLink'] = link

            yield Request(link, callback = self.parseDescription, meta={'item':item})

        ############################################################################
        # going for next pages

        next_page = ''.join(response.xpath('//a[@class="next_page fr"]/@href').extract())
        #We stored already crawled link in this list
        crawledLinks = []
        #Pattern to check proper link
        cur_link = urlparse.urljoin(response.url, next_page.strip())
        #if it is a proper link and is not checked yet, yield it to the Spider
        if cur_link!='' and not cur_link in crawledLinks:
            crawledLinks.append(cur_link )
            yield Request(cur_link , self.parse4)

    def parseDescription(self, response):
        #open_in_browser(response)
        item = response.meta['item']

        item['region'] = ''.join(response.xpath("//dt[text()='%s']/following::dd[1]/text()" % u"Регион").extract())

        bodyType= ''.join(response.xpath("//dt[text()='%s']/following::dd[1]/text()" % u"Кузов").extract())
        bodyType= re.sub('[\n()]', '', bodyType)
        item['bodyType'] = bodyType.strip()

        header = response.xpath('//header')
        AdTitle = ''.join(header.xpath('h1[@itemprop="name"]/text()').extract())

        carMakeSyntax = '//div[@class="left-column"]/div[@class="breadcrumbs"]/div/a[contains(substring-after(substring-after(@href, "/cars/"),"/"),"/") and not(contains(substring-after(substring-after(substring-after(@href, "/cars/"),"/"),"/"),"/"))]/descendant::span[@itemprop="title"]/text()'
        carMakeDiv = response.xpath(carMakeSyntax)
        carMake = ''.join(carMakeDiv.extract()[0])
        item['carMake'] = carMake

        carModelSyntax = '//div[@class="left-column"]/div[@class="breadcrumbs"]/div/a[contains(substring-after(substring-after(substring-after(@href, "/cars/"),"/"),"/"),"/")]/descendant::span[@itemprop="title"]/text()'

        carModel = response.xpath(carModelSyntax).extract()[0]

        if carModel == '':
            carModel = carMake

        item['carModel'] = carModel

        item['carYear'] = AdTitle[len(carModel)+1:len(carModel)+5].encode('ascii', errors='ignore')

        engine = ''.join(response.xpath('//dt[@title="displacement engine-type"]/following::dd[1]/text()').extract())
        engineVol = engine.encode('ascii', errors='ignore')
        engineVol = re.sub('[\n() -]', '', engineVol)
        item['engineVol']  = engineVol.encode('ascii', errors='ignore')

        op = engine.find("(")
        cp = engine.find(")")
        item['engineType']  = engine[op+1:cp]

        transmission = ''.join(response.xpath('//dt[@title="transmission"]/following::dd[1]/text()').extract())
        item['transmission']  = transmission

        condition = ''.join(response.xpath("//dt[text()='%s']/following::dd[1]/text()" % u"Состояние").extract())
        condition = re.sub('[\n()]', '', condition)
        item['condition']  = condition.strip()

        steeringWheel = ''.join(response.xpath("//dt[text()='%s']/following::dd[1]/text()" % u"Руль").extract())
        steeringWheel  = re.sub('[\n()]', '', steeringWheel )
        item['steeringWheel']  = steeringWheel.strip()

        gearType = ''.join(response.xpath("//dt[text()='%s']/following::dd[1]/text()" % u"Привод").extract())
        gearType  = re.sub('[\n()]', '', gearType )
        item['gearType']  = gearType.strip()

        color = ''.join(response.xpath("//dt[text()='%s']/following::dd[1]/text()" % u"Цвет").extract())
        color  = re.sub('[\n()]', '', color)
        color  = re.sub('[  ]', ' ', color)
        item['color']  = color.strip()

        mileage = ''.join(response.xpath("//dt[text()='%s']/following::dd[1]/text()" % u"Пробег").extract())
        mileage = mileage.encode('ascii', errors='ignore')
        mileage = re.sub('[\n() -]', '', mileage)
        item['mileage']  = mileage

        customsState = ''.join(response.xpath('//dt[@class="value-title in-kazahstan"]/following::dd[1]/text()').extract())
        customsState = re.sub('[\n()]', '', customsState)
        item['customsState']  = customsState.strip()

        # crunching configuration data
        configFull = ''.join(response.xpath('//div[@class="configuration-name"]/text()').extract())

        item['configDiscs'] = (configFull.find(u"литые диски")>-1)*1
        item['configDodger'] = (configFull.find(u"обвес")>-1)*1
        item['configTinting'] = (configFull.find(u"тонировка")>-1)*1
        item['configWinch'] = (configFull.find(u"лебёдка")>-1)*1
        item['configHatch'] = (configFull.find(u"люк")>-1)*1
        item['configWindshields'] = (configFull.find(u"ветровики")>-1)*1
        item['configBullbar'] = (configFull.find(u"кенгурятник")>-1)*1
        item['configRailing'] = (configFull.find(u"рейлинги")>-1)*1
        item['configSpoiler'] = (configFull.find(u"спойлер")>-1)*1
        item['configTrunk'] = (configFull.find(u"багажник")>-1)*1

        item['configXenon'] = (configFull.find(u"ксенон")>-1)*1
        item['configHeadlightWasher'] = (configFull.find(u"омыватель фар")>-1)*1
        item['configBiXenon'] = (configFull.find(u"биксенон")>-1)*1
        item['configHeadlightCor'] = (configFull.find(u"корректор фар")>-1)*1
        item['configHeadlightCryst'] = (configFull.find(u"хрустальная оптика")>-1)*1
        item['configMirriorWarm'] = (configFull.find(u"обогрев зеркал")>-1)*1
        item['configAntiFog'] = (configFull.find(u"противотуманки")>-1)*1

        item['configVelour'] = (configFull.find(u"велюр")>-1)*1
        item['configAlcantara'] = (configFull.find(u"алькантара")>-1)*1
        item['configLeather'] = (configFull.find(u"кожа")>-1)*1
        item['configCombi'] = (configFull.find(u"комбинированный")>-1)*1
        item['configWood'] = (configFull.find(u"дерево")>-1)*1
        item['configShields'] = (configFull.find(u"шторки")>-1)*1

        item['configQuadro'] = (configFull.find(u"квадросистема")>-1)*1
        item['configUSB'] = (configFull.find(u"USB")>-1)*1
        item['configCD'] = (configFull.find(u"CD")>-1)*1
        item['configDVD'] = (configFull.find(u"DVD")>-1)*1
        item['configCDChanger'] = (configFull.find(u"CD-чейнджер")>-1)*1
        item['configDVDChanger'] = (configFull.find(u"DVD-чейнджер")>-1)*1
        item['configMP3'] = (configFull.find(u"MP3")>-1)*1
        item['configSubwoofer'] = (configFull.find(u"сабвуфер")>-1)*1

        item['configSteerHydr'] = (configFull.find(u"ГУР")>-1)*1
        item['configBoardPC'] = (configFull.find(u"бортовой компьютер")>-1)*1
        item['configABS'] = (configFull.find(u"ABS")>-1)*1
        item['configNavigation'] = (configFull.find(u"навигационная система")>-1)*1
        item['configSRS'] = (configFull.find(u"SRS")>-1)*1
        item['configBluetooth'] = (configFull.find(u"блютуз")>-1)*1
        item['configWinterMode'] = (configFull.find(u"зимний режим")>-1)*1
        item['configMultiSteer'] = (configFull.find(u"мултируль")>-1)*1
        item['configSportMode'] = (configFull.find(u"спортивный режим")>-1)*1
        item['configSteerWarm'] = (configFull.find(u"подогрев руля")>-1)*1
        item['configTurboPressure'] = (configFull.find(u"турбонаддув")>-1)*1
        item['configSeatWarm'] = (configFull.find(u"подогрев сидений")>-1)*1
        item['configTurboTimer'] = (configFull.find(u"турботаймер")>-1)*1
        item['configSeatMemory'] = (configFull.find(u"память сидений")>-1)*1
        item['configAlarm'] = (configFull.find(u"сигнализация")>-1)*1
        item['configSteerMemory'] = (configFull.find(u"память руля")>-1)*1
        item['configAutoIgnition'] = (configFull.find(u"автозавод")>-1)*1
        item['configParktronics'] = (configFull.find(u"парктроники")>-1)*1
        item['configImmobilizer'] = (configFull.find(u"иммобилайзер")>-1)*1
        item['configRearCam'] = (configFull.find(u"камера заднего вида")>-1)*1
        item['configElectr'] = (configFull.find(u"полный электропакет")>-1)*1
        item['configLightSens'] = (configFull.find(u"датчик света")>-1)*1
        item['configCentralLocker'] = (configFull.find(u"центрозамок")>-1)*1
        item['configRainSensor'] = (configFull.find(u"датчик дождя")>-1)*1
        item['configAC'] = (configFull.find(u"кондиционер")>-1)*1
        item['configTirePressure'] = (configFull.find(u"датчик давления в шинах")>-1)*1
        item['configClimate'] = (configFull.find(u"климат-контроль")>-1)*1
        item['configAirSuspension'] = (configFull.find(u"пневмоподвеска")>-1)*1
        item['configCruise'] = (configFull.find(u"круиз-контроль")>-1)*1
        item['configClearance'] = (configFull.find(u"изменяемый клиренс")>-1)*1

        item['configFreshDriven'] = (configFull.find(u"свежепригнан")>-1)*1
        item['configFreshDelivered'] = (configFull.find(u"свежедоставлен")>-1)*1
        item['configTax'] = (configFull.find(u"налог уплачен")>-1)*1
        item['configMaintenance'] = (configFull.find(u"техосмотр пройден")>-1)*1
        item['configNoInvest'] = (configFull.find(u"вложений не требует")>-1)*1

        AdViews = ''.join(response.xpath('//div[@class="under-desr"][1]/text()').extract())
        AdViews = AdViews.encode('ascii', errors='ignore')
        AdViews = re.sub('[ ]', '', AdViews)
        item['AdViews'] = AdViews.strip()

        PostDate = response.xpath('//div[@class="under-desr"][2]/text()').extract()
        item['PostDate'] = PostDate

        priceValue = ''.join(header.xpath('span[@class="dollar"]/text()').extract())
        priceValue = priceValue.encode('ascii', errors='ignore')
        priceValue = re.sub('[$ ]', '', priceValue)
        item['SellingPrice'] = priceValue

        AdPhone = ''.join(response.xpath('//span[@id="ya_share1"]/@data-desc').extract())
        AdPhone = AdPhone.replace('\n', ' ').replace('\r', '')
        item['AdPhone'] = AdPhone.encode('ascii', errors='ignore')

        return item