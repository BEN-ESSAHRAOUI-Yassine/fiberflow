FROM postgis/postgis:16-3.4

RUN apt-get update && apt-get install -y gdal-bin && rm -rf /var/lib/apt/lists/*

COPY docs/Fake_GIS_data/load-gis-data.sh /usr/local/bin/load-gis-data.sh
RUN chmod +x /usr/local/bin/load-gis-data.sh
