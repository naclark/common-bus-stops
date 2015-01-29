#makebusdb.py
#Simple KCM Shared Stops script by Nick Clark
#Version 0.2, 1/19/2015

import csv, sqlite3
conn = sqlite3.connect('busstuff.db')
c = conn.cursor()

# baleeted = [('stop_times',),('trips',),('stops',),('routes',),('route_stop',),]
# c.executemany('DROP TABLE IF EXISTS ?',baleeted) This doesn't seem to work.
c.execute('DROP TABLE IF EXISTS route_stop')
c.execute('DROP TABLE IF EXISTS stop_times')
c.execute('DROP TABLE IF EXISTS trips')
c.execute('DROP TABLE IF EXISTS stops')
c.execute('DROP TABLE IF EXISTS routes')

conn.commit()
'''
Routes.txt columns of interest: route_id (index 0), route_short_name (2)
and route_desc (4).
'''
c.execute('''CREATE TABLE routes (route_id text, route_short_name text,
            route_desc text, PRIMARY KEY (route_id))''')
with open('/downloads/routes.txt', 'rb') as f:
    reader = csv.reader(f)
    reader.next() #Don't need the column descriptions.
    for row in reader:
        subst = (row[0],row[2],row[4],)
        c.execute("INSERT INTO routes VALUES(?,?,?)", subst)

conn.commit()
'''
Stops.txt columns of interest: stop_id (0), stop_name (2),
stop_lat (4) and stop_lon (5).
'''

c.execute('''CREATE TABLE stops (stop_id text, stop_name text, stop_lat text, stop_lon text)''')

with open('/downloads/stops.txt', 'rb') as f:
    reader = csv.reader(f)
    reader.next()
    for row in reader:
        subst = (row[0],row[2],row[4],row[5],)
        c.execute("INSERT INTO stops VALUES(?,?,?,?)",subst)
        
c.execute('CREATE INDEX stops_stop_id_index ON stops(stop_id)') #Sqlite doesn't
# seem to do keys, so I'll manually create an index.
conn.commit()

# From trips.txt, all we want is route_id (0) and corresponding trip_ids (2).
c.execute('''CREATE TABLE trips (trip_id text, route_id text)''')
with open('/downloads/trips.txt', 'rb') as f:
    reader = csv.reader(f)
    reader.next() #Don't need the column descriptions.
    for row in reader:
        subst = (row[2],row[0],)
        c.execute("INSERT INTO trips VALUES(?,?)",subst)

c.execute('''CREATE INDEX trips_route_id_index ON trips(route_id)''')

conn.commit()

# From stop_times.txt, we want trip_id (0), stop_id (3).
c.execute('''CREATE TABLE stop_times (trip_id text, stop_id text)''')

with open('/downloads/stop_times.txt', 'rb') as f:
    reader = csv.reader(f)
    reader.next() #Don't need the column descriptions.
    for row in reader:
        subst = (row[0],row[3],)
        c.execute("INSERT INTO stop_times VALUES(?,?)", subst)
c.execute('''CREATE INDEX stop_times_trip_id_index ON stop_times(trip_id)''')

conn.commit()

c.execute('''CREATE TABLE route_stop (route_id text, stop_id text)''')

routes = c.execute("SELECT route_id FROM routes").fetchall()

for route in routes:
    stops = c.execute('''SELECT DISTINCT route_id, stops.stop_id FROM trips
            INNER JOIN stop_times ON stop_times.trip_id = trips.trip_id
            INNER JOIN stops ON stops.stop_id = stop_times.stop_id
            WHERE route_id = ?''',route).fetchall()
    c.executemany("INSERT INTO route_stop VALUES(?,?)", stops)

conn.commit()
conn.close()
