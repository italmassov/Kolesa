Sys.setlocale(,"russian")

path.to.data = "C:/Users/kuanysh/Documents/Scrapy/kolesa/last week data - put copies"
last.week.data = list.files(path = path.to.data, pattern = "*.csv",full.names = T)

for(i in 1:length(last.week.data)){
  #i = 1
  cur.file = read.csv(last.week.data[i])
  #head(cur.file)
  
  if(i==1){
    total.files = cur.file
  }else
    total.files = rbind(total.files, cur.file)
}
rm(cur.file)

#save aggregated results
write.csv(total.files, "C:/Users/kuanysh/Documents/Scrapy/kolesa/current data model/aggregated.week.csv", row.names=F)
total.files = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/current data model/aggregated.week.csv")

# create ad level aggregation data
require(dplyr)
week.long.ads = summarise(group_by(total.files, AdLink, carMake, carModel, 
                      carYear, condition, customsState, 
                      engineType, engineVol, gearType, 
                      region, steeringWheel, 
                      transmission, mileage), AggrPrice = mean(SellingPrice, na.rm=T))
head(week.long.ads)
colnames(week.long.ads)[5] = "carCondition"
summary(week.long.ads)

write.csv(week.long.ads, "C:/Users/kuanysh/Documents/Scrapy/kolesa/current data model/week.long.ads.csv",
          row.names=F)