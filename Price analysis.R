Sys.setlocale(, "russian")
# analysis of fair price

file1 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_24.csv", stringsAsFactors = F, nrows=3*10^5)
file2 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_25.csv", stringsAsFactors = F, nrows=3*10^5)
file3 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_26.csv", stringsAsFactors = F, nrows=3*10^5)
file4 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_27.csv", stringsAsFactors = F, nrows=3*10^5)
file5 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_28.csv", stringsAsFactors = F, nrows=3*10^5)
file6 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_29.csv", stringsAsFactors = F, nrows=3*10^5)
file7 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_30.csv", stringsAsFactors = F, nrows=3*10^5)

aggreg.file = rbind(file1, file2, file3, file4, file5, file6, file7)


write.csv(aggreg.file, "C:/Users/kuanysh/Documents/Scrapy/kolesa/aggreg.file.csv", row.names=F)
aggreg.file = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/aggreg.file.csv")

# descriptive statistic on number of days up
require(dplyr)
#head(aggreg.file)
#colnames(aggreg.file)
#links = summarize(group_by(aggreg.file, AdLink, AdPhone), days=length(crawlDateTime), 
#                  totalViews = sum(AdViews))

# check for repeating Ad link with different number
#links.stat =  summarize(group_by(links, AdLink), count=length(AdPhone))
#links.stat[which(links.stat$count > 1),]

#test.data = aggreg.file[which(aggreg.file$AdLink == "http://kolesa.kz/a/show/10157640"), ] # same shit
#test.data = aggreg.file[which(aggreg.file$AdLink == "http://kolesa.kz/a/show/10282661"), ] # same shit
#SUMMARY: so we asssume that AdLink is unique identifier of the ad

#Check if number of views correspond with days on site
#plot(links$days, links$totalViews) 

# total number of views is not characteristic for some ads that stood long
# instead get ads that grow fastest in viewership
require(dplyr)
aggreg.file = arrange(aggreg.file, crawlDateTime )
aggreg.file = aggreg.file %.%
  group_by(AdLink) %.%
  mutate(dod.views = AdViews - lag(AdViews, default=NA))

filter(aggreg.file[c("AdLink","crawlDateTime", "AdViews", "dod.views")], AdLink=="http://kolesa.kz/a/show/10157640")

linksViews = summarize(group_by(aggreg.file, AdLink), days=length(crawlDateTime),
                       totalViews = sum(dod.views, na.rm=T))
head(linksViews)
plot(linksViews$days, linksViews$totalViews)
boxplot(totalViews~days,data=linksViews, outline=F)

linksViews$views.per.day = linksViews$totalViews/linksViews$days
hist(linksViews$views.per.day)

###############################################
# Model build attempts

#Attempt #1: Build OLS model for each separate carModel with all data incorporated
which(colnames(aggreg.file)== "sellingPrice")

SpecsAndPrices = summarise(group_by(aggreg.file, region, carMake, carModel, carYear, condition, customsState, 
                          transmission, engineVol, mileage, AdLink, SellingPrice), days=length(AdLink))

head(SpecsAndPrices)

SpecsAndPricesCamry = filter(SpecsAndPrices, carModel=="Toyota Camry" & carYear==2011)
SpecsAndPricesCamry = SpecsAndPrices[which(SpecsAndPrices$carModel == "Toyota Camry" & SpecsAndPrices$carYear==2011), ]

length(SpecsAndPricesCamry[,1])
head(SpecsAndPricesCamry)
hist(SpecsAndPricesCamry$SellingPrice)

SpecsAndPricesCamrylm = lm(SellingPrice~condition+customsState+transmission+engineVol+mileage, data=SpecsAndPricesCamry)
summary(SpecsAndPricesCamrylm)

head(linksViews)
head(SpecsAndPricesCamry)

SpecsAndPricesCamry = merge(SpecsAndPricesCamry, 
                            linksViews[c("AdLink", "views.per.day")], by="AdLink")

SpecsAndPricesCamrylm = lm(SellingPrice~condition+customsState+transmission+engineVol, data=SpecsAndPricesCamry, 
                           weights = ifelse(views.per.day>0, 1/views.per.day, 0))
summary(SpecsAndPricesCamrylm)
