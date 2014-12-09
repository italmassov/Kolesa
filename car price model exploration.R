###############################
# car price model exploration

# load data
week.long.ads = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/current data model/week.long.ads.csv")

week.long.ads$carYear = as.numeric(as.character(week.long.ads$carYear))
week.long.ads$engineVol = as.numeric(as.character(week.long.ads$engineVol))
week.long.ads$mileage = as.numeric(as.character(week.long.ads$mileage))

week.long.ads.clean = week.long.ads[which(week.long.ads$carModel !=""), ]
length(levels(week.long.ads.clean$carModel))

week.long.ads.clean$carModel = factor(week.long.ads.clean$carModel)

# step 0: run anova to identify impact of each factor
#ads.aov = aov(AggrPrice~carMake+carModel+carYear+carCondition+customsState+
#                engineType+engineVol+gearType+region+steeringWheel+
#                transmission+mileage, data=week.long.ads.clean )

# step 1 - try linear regression model on each model
length(levels(week.long.ads.clean$carModel))

for(i in length(levels(week.long.ads.clean$carModel))){
  #i = 2149
  cur.model.data = week.long.ads.clean[which(week.long.ads.clean$carModel == 
                      levels(week.long.ads.clean$carModel)[i]), ]
  
  cur.model.data= droplevels(cur.model.data)
  
  if(length(cur.model.data[,1])>30){    
    #make shorter model of data with two or more levels
    par.levels = rep(0, length(colnames(cur.model.data))-1)
    
    for(k in 2:(length(colnames(cur.model.data))-1)){
      #k=
      par.levels[k] = length(table(cur.model.data[, k]))
    }
    
    cur.model.short = cur.model.data[c(which(par.levels>=2), length(colnames(cur.model.data)))]
    rownames(cur.model.short) = NULL
    
    # runing regular ols
    cur.model.lm = lm(AggrPrice~., data=cur.model.short)
    summary(cur.model.lm)
    
    par(mfrow=c(2,2))
    plot(cur.model.lm)
    par(mfrow=c(1,1))    
    
    # predict price for particular parameters
    cur.model.sim = cur.model.data[0,]
    
    # start with Year of production
    require(dplyr)
    years.list = summarize(group_by(cur.model.data, carYear), F)[,1]
    conditions.list = summarize(group_by(cur.model.data, carCondition), F)[,1]
    customs.list = summarize(group_by(cur.model.data, customsState), F)[,1]
    engine.list = summarize(group_by(cur.model.data, engineType), F)[,1]
    engineVol.list = summarize(group_by(cur.model.data, engineVol), F)[,1]
    gear.list = summarize(group_by(cur.model.data, gearType), F)[,1]
    region.list = summarize(group_by(cur.model.data, region), F)[,1]
    steering.list = summarize(group_by(cur.model.data, steeringWheel), F)[,1]
    transmission.list = summarize(group_by(cur.model.data, transmission), F)[,1]
    mileage.list = c(0, 1000, 10000, 50000, 100000, 250000, 500000)
    
    conditions.list = conditions.list[which(conditions.list != "")]
    customs.list = conditions.list[which(customs.list != "")]
    engine.list = conditions.list[which(engine.list != "")]
    engineVol.list = conditions.list[which(engineVol.list != "")]
    gear.list = conditions.list[which(gear.list != "")]
    region.list = conditions.list[which(region.list != "")]
    steering.list = conditions.list[which(steering.list != "")]
    transmission.list = conditions.list[which(transmission.list != "")]

    cur.model.sim = expand.grid(carYear = years.list, carCondition = conditions.list)    
    
    cur.model.sim$customsState = NA
    cur.model.sim$engineType = NA    
    cur.model.sim$engineVol = NA
    cur.model.sim$gearType = NA
    cur.model.sim$region = NA
    cur.model.sim$steeringWheel = NA
    cur.model.sim$transmission = NA
    cur.model.sim$mileage = 50000
    
    modelInterc = as.numeric(coef(cur.model.lm)["(Intercept)"])
    modelCarYear= as.numeric(coef(cur.model.lm)["carYear"])
    modelCondition= as.numeric(coef(cur.model.lm)[3])
    modelMileage = as.numeric(coef(cur.model.lm)["mileage"])
    
    iconv(names(coef(cur.model.lm)), from="utf-8", to="windows-1251")
    
    # calculate basic option
    cur.model.sim$basic.price = modelInterc +  cur.model.sim$carYear*modelCarYear +
       modelCondition +   cur.model.sim$mileage*modelMileage
    
    cur.model.sim$PredPrice = predict(cur.model.lm, newdata = cur.model.sim)
    
    # calculate using nnls
    # running non-negative least squares
    #install.packages("nnls")
    require(nnls)
    head(cur.model.short)
    
    cur.model.short.num = cur.model.short
    cur.model.short.num$customsState = as.numeric(cur.model.short.num$customsState)
    cur.model.short.num$gearType = as.numeric(cur.model.short.num$gearType)
    cur.model.short.num$region = as.numeric(cur.model.short.num$region)
    cur.model.short.num$transmission = as.numeric(cur.model.short.num$transmission)    
    
    A = as.matrix(cbind(1, cur.model.short.num[, c("carYear", "customsState", "engineVol", "gearType", 
                                          "region", "transmission")]))
    b.elec = cur.model.short.num$AggrPrice
    
    nnls.price = nnls(A, b.elec)
    coef(nnls.price)
    
    modelInterc = coef(nnls.price)[1]
    modelCarYear = coef(nnls.price)[2]
    
    cur.model.sim$basic.price = modelInterc +  cur.model.sim$carYear*modelCarYear +
      modelCondition +   cur.model.sim$mileage*modelMileage
    
    
  }
}