#busui.py
#Simple UI for KCM Shared Stops
#Version 0.2, 1/26/2015

import sqlite3
conn = sqlite3.connect('busstuff.db')
c = conn.cursor()

#routes = c.execute('SELECT route_short_name, route_id FROM routes').fetchall()
#routes = dict(routes)

print "Welcome to a simple UI for King County Metro shared stops."
running = True
while running:
    selection1 = raw_input("Type in a route number, or 'quit' to exit: ")
    if selection1 == 'quit':
        running = False
        break
    else:
        #stops1 = c.execute('''SELECT stop_id FROM route_stop WHERE route_id=?''',
        #                   (routes[selection1],)).fetchall()
        selection2 = raw_input("Now tell me a second route number: ")
        #stops2 = c.execute('''SELECT DISTINCT stop_id FROM route_stop
        #                        WHERE route_id=?''',(selection2,)).fetchall()
        #commonstops = list(set(stops1) & set(stops2))
        #for i in commonstops:
        print c.execute('''select stop_name, stop_lon, stop_lat from route_stop rs 
        inner join routes r on rs.route_id = r.route_id
        inner join stops s on rs.stop_id = s.stop_id 
        where r.route_short_name in (?,?) 
        group by rs.stop_id 
        having count(rs.stop_id)>1''',(selection1,selection2,)).fetchall()
