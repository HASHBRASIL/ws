CREATE FUNCTION "randompoint"(IN geom "geometry", IN maxiter int4 DEFAULT 1000) RETURNS "geometry"
	AS $BODY$
DECLARE
        i INTEGER := 0;
        x0 DOUBLE PRECISION;
        dx DOUBLE PRECISION;
        y0 DOUBLE PRECISION;
        dy DOUBLE PRECISION;
        xp DOUBLE PRECISION;
        yp DOUBLE PRECISION;
        rpoint Geometry;
BEGIN
        -- find envelope
        x0 = ST_XMin(geom);
        dx = (ST_XMax(geom) - x0);
        y0 = ST_YMin(geom);
        dy = (ST_YMax(geom) - y0);

        WHILE i < maxiter LOOP
                i = i + 1;
                xp = x0 + dx * random();
                yp = y0 + dy * random();
                rpoint = ST_SetSRID( ST_MakePoint( xp, yp ), ST_SRID(geom) );
                EXIT WHEN ST_Within( rpoint, geom );
        END LOOP;

        IF i >= maxiter THEN
                RAISE EXCEPTION 'RandomPoint: number of interations exceeded %', maxiter;
        END IF;

        RETURN rpoint;
END;
$BODY$
	LANGUAGE plpgsql
	COST 100
	CALLED ON NULL INPUT
	SECURITY INVOKER
	VOLATILE;
ALTER FUNCTION ="randompoint"(IN geom "geometry", IN maxiter int4) OWNER TO "hash";





insert into tb_servico VALUES ('cebc11e0-8017-4a32-9837-141e2ae2b684', null, 'Modulo - Mapa', null, 'Modulo - Mapa', 'Modulo - Mapa', null, null, null, true, null, 'mapa/index/index');
insert into tb_servico VALUES ('0a733776-758e-4573-8bb8-3c45ea9fb35c', null, 'Principal', null, 'Principal', 'Principal', null, 'cebc11e0-8017-4a32-9837-141e2ae2b684', null, true, null, 'mapa/index/index');








-- bf30e8ba-90f1-4095-a3ba-1ca61b4e98a2



