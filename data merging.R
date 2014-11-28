Sys.setlocale(,"russian")

file1 = read.table("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_24_cor.csv", stringsAsFactors = F, sep=",", dec=".", header = T, encoding = "UTF-8")
file1 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_24_cor.csv", stringsAsFactors = F)
#file1 = read.table("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_22.csv", stringsAsFactors = F, sep=",", dec=".", header = T, encoding = "utf-8")

head(file1)

#file1$region = enc2utf8(x = file1$region)

#table(file1$carMake)

#total.file = rbind(file1)
#levels(file1$region)
#write.csv(total.file, "C:/Users/kuanysh/Documents/Scrapy/kolesa/total_data.csv", row.names=F)
#test_file = file1[1:100,]
#write.csv(test_file, "C:/Users/kuanysh/Documents/Scrapy/kolesa/test_file.csv", row.names=F)

# create aggregation file
require(dplyr)
total.file = group_by(file1, carMake, carModel, carYear, condition, customsState, 
                      engineType, engineVol, gearType, region, steeringWheel, 
                      transmission, mileage)
total.file.aggr =  summarise(total.file, median = median(SellingPrice), 
                             quant25 = quantile(SellingPrice, 0.25), 
                             quant75 = quantile(SellingPrice, 0.75))

colnames(total.file.aggr)[4] = "carCondition"
colnames(total.file.aggr)

write.csv(total.file.aggr, "C:/Users/kuanysh/Documents/Scrapy/kolesa/carsAggregate.csv",
          row.names=F, fileEncoding="utf-8")

# check specific errors
str(total.file$engineVol)

levels(total.file$engineVol)

spec = subset(total.file, carMake=='Универсалы')
spec$AdLink
