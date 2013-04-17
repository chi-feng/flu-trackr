
from scipy import stats
import csv

import time
import datetime

def strtotime(str):
  return time.mktime(datetime.datetime.strptime(str, "%Y-%m-%d").timetuple())

data = open('data.txt')

names = []
trends = []

for line_number, line in enumerate(data):
  reader = csv.reader([line])
  tokens = list(reader)[0]
  if line_number == 0:
    names = [token for token in tokens]
    trends = [[] for token in tokens]
  else:
    timestamp = 0
    for i, token in enumerate(tokens):
      if i == 0:
        timestamp = strtotime(token);
      if i > 0:
        value = 0 if token == '' else float(token)
        trends[i].append([timestamp, value])
      
def normalize_trends():
  # 0 is date, 1 is US, followed by 50 states
  for i in range(1, 52):
    timestamps, values = zip(*trends[i]);
    # 104 is 2x52 weeks/year, so two years
    baseline = stats.scoreatpercentile(values[-104:], 30)
    trends[i] = [[timestamps[j], values[j] / baseline] for j in range(len(trends[i]))]
    
def save_trends():
  for i in range(1, 52):
    name = names[i]
    print name.replace(" ", "-");
    trend = trends[i]
    filename = 'trends/' + name.replace(" ", "-") + '.txt';
    out = open(filename, 'w')
    for i in range(len(trend)-53, len(trend)):
      data = trend[i]
      out.write('%d %0.2f\n' % (data[0], data[1]))
    out.close()

normalize_trends()
save_trends()

out = open('trends/national.txt', 'w')
for i in range(1, 52):
  name = names[i]
  name = name.replace(" ", "-");
  trend = trends[i]
  out.write('%s %0.2f\n' % (name, trend[-1][1]))
out.close()
