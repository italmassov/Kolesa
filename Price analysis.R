Sys.setlocale(, "russian")
# analysis of fair price
# step 1 - extract data about price with longest period in each file and merge
file1 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_24.csv", stringsAsFactors = F)
for(i in 1:length(file1[1,])){
  file1[,i] = iconv(file1[,i], from = "utf-8", "windows-1251")  
}
head(file1)

file2 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_25.csv", stringsAsFactors = F)
for(i in 1:length(file2[1,])){
  file2[,i] = iconv(file2[,i], from = "utf-8", "windows-1251")  
}
head(file2)

file3 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_26.csv", stringsAsFactors = F)
for(i in 1:length(file3[1,])){
  file3[,i] = iconv(file3[,i], from = "utf-8", "windows-1251")  
}
head(file3)

file4 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_27.csv", stringsAsFactors = F)
for(i in 1:length(file4[1,])){
  file4[,i] = iconv(file4[,i], from = "utf-8", "windows-1251")  
}
head(file4)

file5 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_28.csv", stringsAsFactors = F)
for(i in 1:length(file5[1,])){
  file5[,i] = iconv(file5[,i], from = "utf-8", "windows-1251")  
}
head(file5)

file6 = read.csv("C:/Users/kuanysh/Documents/Scrapy/kolesa/data_11_29.csv", stringsAsFactors = F)
for(i in 1:length(file6[1,])){
  file6[,i] = iconv(file6[,i], from = "utf-8", "windows-1251")  
}
head(file6)


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
#require(dplyr)
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

###############################################
# Model build attempts

#Attempt #1: Build OLS model for each separate carModel with all data incorporated
